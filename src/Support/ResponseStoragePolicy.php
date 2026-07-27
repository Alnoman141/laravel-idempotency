<?php

namespace Alnoman141\LaravelIdempotency\Support;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ResponseStoragePolicy
{
    public function shouldStore(Response $response): bool
    {
        $status = $response->getStatusCode();

        if (config('idempotency.response.store_successful_only')) {
            if ($status >= 200 && $status < 300) {
                return true;
            }
        }

        if (in_array(
            $status,
            config('idempotency.response.store_status_codes', []),
            true
        )) {
            return true;
        }

        if (in_array(
            $status,
            config('idempotency.response.ignore_status_codes', []),
            true
        )) {
            return false;
        }

        if (strlen($response->getContent()) > config('idempotency.response.max_body_size')) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');

        if (
            $contentType === null ||
            ! str_contains($contentType, 'application/json')
        ) {
            return false;
        }

        if (
            $response instanceof StreamedResponse ||
            $response instanceof BinaryFileResponse
        ) {
            return false;
        }

        return false;
    }
}
