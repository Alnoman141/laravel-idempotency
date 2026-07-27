<?php

namespace Alnoman141\LaravelIdempotency\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IdempotencyConflictDetected
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
    ) {}
}