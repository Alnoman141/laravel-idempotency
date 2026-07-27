<?php

namespace alnoman141\LaravelIdempotency\Contracts;

interface IdempotencyLock
{
    public function acquire(string $key): bool;

    public function release(string $key): void;
}