<?php

namespace alnoman141\LaravelIdempotency\Locks;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Lock;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyLock;

class CacheLock implements IdempotencyLock
{
    /** @var array<string, Lock> */
    protected array $locks = [];

    public function __construct(
        protected CacheFactory $cache
    ) {}

    public function acquire(string $key): bool
    {
        $seconds = config('idempotency.lock_timeout', 10);

        $lock = $this->cache
            ->store()
            ->lock(
                'idempotency-lock:' . $key,
                $seconds
            );

        if (! $lock->get()) {
            return false;
        }

        $this->locks[$key] = $lock;

        return true;
    }

    public function release(string $key): void
    {
        $this->locks[$key]?->release();

        unset($this->locks[$key]);
    }
}
