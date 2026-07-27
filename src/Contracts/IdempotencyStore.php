<?php

namespace Alnoman141\LaravelIdempotency\Contracts;

use Alnoman141\LaravelIdempotency\Data\IdempotencyRecord;

interface IdempotencyStore
{
    public function find(string $key): ?IdempotencyRecord;

    public function put(
        string $key,
        IdempotencyRecord $record,
        int $ttl
    ): void;

    public function forget(string $key): void;
}