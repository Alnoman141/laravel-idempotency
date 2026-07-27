<?php

namespace Alnoman141\LaravelIdempotency;

use Illuminate\Contracts\Container\Container;
use Alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use Alnoman141\LaravelIdempotency\Stores\CacheStore;
use Alnoman141\LaravelIdempotency\Stores\DatabaseStore;

class IdempotencyStoreManager
{
    public function __construct(
        protected Container $app
    ) {
    }

    public function driver(): IdempotencyStore
    {
        return match (config('idempotency.driver')) {

            'database' => $this->app->make(DatabaseStore::class),

            default => $this->app->make(CacheStore::class),

        };
    }
}