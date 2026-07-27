<?php

namespace alnoman141\LaravelIdempotency\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyLock;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;
use alnoman141\LaravelIdempotency\Support\RequestFingerprint;
use alnoman141\LaravelIdempotency\Exceptions\IdempotencyConflictException;
use alnoman141\LaravelIdempotency\Exceptions\MissingIdempotencyKeyException;
use alnoman141\LaravelIdempotency\Support\ResponseStoragePolicy;
use alnoman141\LaravelIdempotency\Support\MiddlewareOptions;

use alnoman141\LaravelIdempotency\Events\IdempotencyStarted;
use alnoman141\LaravelIdempotency\Events\IdempotencyCompleted;
use alnoman141\LaravelIdempotency\Events\IdempotencyReplayed;
use alnoman141\LaravelIdempotency\Events\IdempotencyConflictDetected;
use alnoman141\LaravelIdempotency\Events\IdempotencyFailed;
use alnoman141\LaravelIdempotency\Support\IdempotencyKeyValidator;

final class IdempotencyMiddleware
{
    public function __construct(
        private readonly IdempotencyStore $store,
        private readonly RequestFingerprint $fingerprint,
        private readonly IdempotencyLock $lock,
        private readonly ResponseStoragePolicy $responsePolicy,
        private readonly MiddlewareOptions $options,
        private readonly IdempotencyKeyValidator $keyValidator,
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        ?string $ttl = null
    ): Response {

        $header = config(
            'idempotency.header',
            'Idempotency-Key'
        );

        $ttl = filter_var($ttl, FILTER_VALIDATE_INT);

        if ($ttl === false || $ttl <= 0) {
            $ttl = (int) config('idempotency.ttl', 3600);
        }


        $key = $request->header(
            config('idempotency.header', 'Idempotency-Key')
        );

        if ($key === null) {
            return $next($request);
        }

        $this->keyValidator->validate($key);

        if (! $this->lock->acquire($key)) {

            return response()->json([
                'message' => 'Another request with this idempotency key is already processing.',
            ], 409);
        }

        try {

            $fingerprint = $this->fingerprint->generate($request);

            event(new IdempotencyStarted(
                $key,
                $fingerprint,
                $request,
            ));

            /*
            |--------------------------------------------------------------------------
            | Check Existing Record
            |--------------------------------------------------------------------------
            */

            $existing = $this->store->find($key);

            if ($existing !== null) {

                if (!hash_equals(
                    $existing->fingerprint,
                    $fingerprint
                )) {

                    event(new IdempotencyConflictDetected($key));

                    return response()->json([
                        'message' => 'This idempotency key has already been used for a different request.',
                    ], 409);
                }

                if ($existing->status === IdempotencyStatus::Processing) {

                    return response()->json([
                        'message' => 'This request is already being processed.',
                    ], 409);
                }

                if ($existing->status === IdempotencyStatus::Completed) {

                    event(new IdempotencyReplayed($key));
                    return response(
                        $existing->body,
                        $existing->statusCode,
                        $existing->headers
                    );
                }

                // Failed requests are allowed to continue.

            }

            /*
            |--------------------------------------------------------------------------
            | Mark as Processing
            |--------------------------------------------------------------------------
            */

            $this->store->put(
                key: $key,
                record: new IdempotencyRecord(
                    fingerprint: $fingerprint,
                    status: IdempotencyStatus::Processing,
                ),
                ttl: $ttl
            );

            /*
            |--------------------------------------------------------------------------
            | Execute Controller
            |--------------------------------------------------------------------------
            */

            try {

                $response = $next($request);
            } catch (\Throwable $exception) {

                event(new IdempotencyFailed(
                    $key,
                    $exception,
                ));

                $this->store->put(
                    key: $key,
                    record: new IdempotencyRecord(
                        fingerprint: $fingerprint,
                        status: IdempotencyStatus::Failed,
                    ),
                    ttl: $ttl
                );

                throw $exception;
            }

            /*
            |--------------------------------------------------------------------------
            | Store Completed Response
            |--------------------------------------------------------------------------
            */

            if ($this->responsePolicy->shouldStore($response)) {

                $this->store->put(
                    key: $key,
                    record: new IdempotencyRecord(
                        fingerprint: $fingerprint,
                        status: IdempotencyStatus::Completed,
                        statusCode: $response->getStatusCode(),
                        headers: $response->headers->all(),
                        body: $response->getContent(),
                    ),
                    ttl: $ttl
                );
            }

            event(new IdempotencyCompleted(
                $key,
                $response,
            ));

            return $response;
        } finally {

            $this->lock->release();
        }
    }
}
