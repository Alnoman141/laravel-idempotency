<?php

namespace Alnoman141\LaravelIdempotency\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class IdempotencyStarted
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
        public readonly string $fingerprint,
        public readonly Request $request,
    ) {}
}