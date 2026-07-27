<?php

namespace Alnoman141\LaravelIdempotency\Stores;

use Illuminate\Contracts\Cache\Repository;
use Alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use Alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use Alnoman141\LaravelIdempotency\Contracts\FlushableStore;

final class CacheStore implements IdempotencyStore, FlushableStore
{
    public function __construct(
        private readonly Repository $cache,
    ) {
    }

    public function flush(): void
    {
        cache()->flush();
    }

    public function find(string $key): ?IdempotencyRecord
    {
        $data = $this->cache->get($this->storageKey($key));

        if ($data === null) {
            return null;
        }

        return IdempotencyRecord::fromArray($data);
    }

    public function put(
        string $key,
        IdempotencyRecord $record,
        int $ttl
    ): void {
        $this->cache->put(
            $this->storageKey($key),
            $record->toArray(),
            $ttl
        );
    }

    public function forget(string $key): void
    {
        $this->cache->forget(
            $this->storageKey($key)
        );
    }

    private function storageKey(string $key): string
    {
        return 'idempotency:' . hash('sha256', $key);
    }
}