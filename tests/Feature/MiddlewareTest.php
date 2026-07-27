<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Cache::flush();

    Route::post('/orders', function () {
        return response()->json([
            'created' => true,
            'time' => now()->timestamp,
        ]);
    })->middleware('idempotency');
});

it('executes first request', function () {

    $response = $this->postJson(
        '/orders',
        [],
        [
            'Idempotency-Key' => 'abc-123',
        ]
    );

    $response
        ->assertOk()
        ->assertJson([
            'created' => true,
        ]);
});

it('returns the stored response when the same request is repeated', function () {

    $headers = [
        'Idempotency-Key' => 'same-key',
    ];

    $first = $this->postJson(
        '/orders',
        [],
        $headers
    );

    $second = $this->postJson(
        '/orders',
        [],
        $headers
    );

    $first->assertOk();
    $second->assertOk();

    expect($second->json())
        ->toEqual($first->json());
});

it('rejects a different request using the same idempotency key', function () {

    $headers = [
        'Idempotency-Key' => 'duplicate-key',
    ];

    $this->postJson(
        '/orders',
        [
            'amount' => 100,
        ],
        $headers
    )->assertOk();

    $this->postJson(
        '/orders',
        [
            'amount' => 200,
        ],
        $headers
    )->assertStatus(409);
});

it('allows requests without an idempotency key', function () {

    $first = $this->postJson('/orders');

    $second = $this->postJson('/orders');

    $first->assertOk();
    $second->assertOk();
});

it('accepts different idempotency keys', function () {

    $this->postJson(
        '/orders',
        [],
        [
            'Idempotency-Key' => 'key-1',
        ]
    )->assertOk();

    $this->postJson(
        '/orders',
        [],
        [
            'Idempotency-Key' => 'key-2',
        ]
    )->assertOk();
});

it('accepts the same payload after the key has been forgotten', function () {

    $headers = [
        'Idempotency-Key' => 'forget-key',
    ];

    $this->postJson(
        '/orders',
        [],
        $headers
    )->assertOk();

    app(
        \alnoman141\LaravelIdempotency\Contracts\IdempotencyStore::class
    )->forget('forget-key');

    $this->postJson(
        '/orders',
        [],
        $headers
    )->assertOk();
});
