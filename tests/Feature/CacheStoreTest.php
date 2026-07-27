<?php

use Alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use Alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;
use Alnoman141\LaravelIdempotency\Stores\CacheStore;

it('stores and retrieves records', function () {

    $store = app(CacheStore::class);

    $record = new IdempotencyRecord(
        fingerprint: 'abc',
        status: IdempotencyStatus::Completed,
        statusCode: 200,
        headers: [],
        body: 'OK'
    );

    $store->put('key-1', $record, 3600);

    $saved = $store->find('key-1');

    expect($saved)->not->toBeNull();
    expect($saved->body)->toBe('OK');

});