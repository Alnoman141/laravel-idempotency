<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {

    Route::post('/security-test', function () {

        return response()->json([
            'success' => true,
        ]);

    })->middleware('idempotency');

    Route::post('/large-response', function () {

        return response()->make(
            str_repeat('A', 2 * 1024 * 1024), // 2MB
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );

    })->middleware('idempotency');

    Route::post('/stream-response', function () {

        return response()->stream(function () {

            echo 'stream';

        });

    })->middleware('idempotency');

});

it('rejects an empty idempotency key', function () {

    $this->postJson(
        '/security-test',
        [],
        [
            'Idempotency-Key' => '',
        ]
    )->assertStatus(400);

});

it('rejects a key longer than the configured maximum length', function () {

    config()->set(
        'idempotency.key.max_length',
        255
    );

    $key = str_repeat('A', 256);

    $this->postJson(
        '/security-test',
        [],
        [
            'Idempotency-Key' => $key,
        ]
    )->assertStatus(400);

});

it('rejects invalid characters in the key', function () {

    $this->postJson(
        '/security-test',
        [],
        [
            'Idempotency-Key' => 'abc<>123',
        ]
    )->assertStatus(400);

});

it('rejects fingerprint mismatch using the same key', function () {

    $headers = [
        'Idempotency-Key' => 'duplicate-key',
    ];

    $this->postJson(
        '/security-test',
        [
            'amount' => 100,
        ],
        $headers
    )->assertOk();

    $this->postJson(
        '/security-test',
        [
            'amount' => 200,
        ],
        $headers
    )->assertStatus(409);

});

it('does not cache responses larger than the configured limit', function () {

    config()->set(
        'idempotency.response.max_body_size',
        1024
    );

    $headers = [
        'Idempotency-Key' => 'large-response',
    ];

    $this->post(
        '/large-response',
        [],
        $headers
    )->assertOk();

    $this->post(
        '/large-response',
        [],
        $headers
    )->assertOk();

});

it('does not cache streamed responses', function () {

    $headers = [
        'Idempotency-Key' => 'stream-response',
    ];

    $this->post(
        '/stream-response',
        [],
        $headers
    )->assertOk();

    $this->post(
        '/stream-response',
        [],
        $headers
    )->assertOk();

});

it('accepts a valid uuid idempotency key', function () {

    $this->postJson(
        '/security-test',
        [],
        [
            'Idempotency-Key' => '550e8400-e29b-41d4-a716-446655440000',
        ]
    )->assertOk();

});