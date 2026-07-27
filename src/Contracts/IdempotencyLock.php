<?php

namespace Alnoman141\LaravelIdempotency\Contracts;

interface IdempotencyLock
{
    public function acquire(string $key): bool;

    public function release(): void;
}