<?php

namespace Alnoman141\LaravelIdempotency\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyCompleted
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
        public readonly Response $response,
    ) {}
}