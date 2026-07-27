<?php

namespace Alnoman141\LaravelIdempotency\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IdempotencyFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
        public readonly \Throwable $exception,
    ) {}
}