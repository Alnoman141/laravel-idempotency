<?php

namespace alnoman141\LaravelIdempotency\Locks;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Lock;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyLock;

class CacheLock implements IdempotencyLock
{
    protected ?Lock $lock = null;

    public function __construct(
        protected CacheFactory $cache
    ) {}

    public function acquire(string $key): bool
    {
        $seconds = config('idempotency.lock_timeout', 10);

        $this->lock = $this->cache
            ->store()
            ->lock(
                'idempotency-lock:' . $key,
                $seconds
            );

        return $this->lock->get();
    }

    public function release(): void
    {
        $this->lock?->release();
    }
}
