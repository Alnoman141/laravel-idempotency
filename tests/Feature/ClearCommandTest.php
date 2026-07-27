<?php

use Illuminate\Support\Facades\Artisan;

it('clears all idempotency records', function () {

    $store = app(
        \Alnoman141\LaravelIdempotency\Contracts\IdempotencyStore::class
    );

    $store->put(
        'abc',
        new \Alnoman141\LaravelIdempotency\Data\IdempotencyRecord(
            fingerprint: '123',
            status: \Alnoman141\LaravelIdempotency\Enums\IdempotencyStatus::Completed,
            statusCode: 200,
            headers: [],
            body: '{}',
        ),
        3600
    );

    Artisan::call('idempotency:clear');

    expect(
        $store->find('abc')
    )->toBeNull();

});