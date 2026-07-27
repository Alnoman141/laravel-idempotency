<?php

namespace alnoman141\LaravelIdempotency\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IdempotencyReplayed
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
    ) {}
}
