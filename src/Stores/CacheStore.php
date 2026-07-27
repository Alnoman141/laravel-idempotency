<?php

namespace alnoman141\LaravelIdempotency\Stores;

use Illuminate\Contracts\Cache\Repository;
use alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use alnoman141\LaravelIdempotency\Contracts\FlushableStore;

final class CacheStore implements IdempotencyStore, FlushableStore
{
    public function __construct(
        private readonly Repository $cache,
    ) {}

    private const INDEX_KEY = 'idempotency:_index';

    public function flush(): void
    {
        $index = $this->cache->get(self::INDEX_KEY, []);

        foreach (array_keys($index) as $storageKey) {
            $this->cache->forget($storageKey);
        }

        $this->cache->forget(self::INDEX_KEY);
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
        $storageKey = $this->storageKey($key);

        $this->cache->put($storageKey, $record->toArray(), $ttl);

        $index = $this->cache->get(self::INDEX_KEY, []);
        $index[$storageKey] = true;
        $this->cache->forever(self::INDEX_KEY, $index);
    }

    public function forget(string $key): void
    {
        $storageKey = $this->storageKey($key);

        $this->cache->forget($storageKey);

        $index = $this->cache->get(self::INDEX_KEY, []);
        unset($index[$storageKey]);
        $this->cache->forever(self::INDEX_KEY, $index);
    }

    private function storageKey(string $key): string
    {
        return 'idempotency:' . hash('sha256', $key);
    }
}