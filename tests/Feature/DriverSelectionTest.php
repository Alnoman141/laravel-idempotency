<?php

use alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use alnoman141\LaravelIdempotency\Stores\CacheStore;

it('resolves cache driver', function () {

    config()->set('idempotency.driver', 'cache');

    expect(app(IdempotencyStore::class))
        ->toBeInstanceOf(CacheStore::class);
});
