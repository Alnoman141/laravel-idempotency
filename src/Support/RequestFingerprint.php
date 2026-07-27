<?php

namespace Alnoman141\LaravelIdempotency\Support;

use Illuminate\Http\Request;

final class RequestFingerprint
{
    public function generate(Request $request): string
    {
        $payload = [
            'method' => strtoupper($request->method()),
            'path' => $request->path(),
            'query' => $request->query(),
            'body' => $request->all(),
        ];

        return hash(
            'sha256',
            json_encode(
                $payload,
                JSON_THROW_ON_ERROR
            )
        );
    }
}