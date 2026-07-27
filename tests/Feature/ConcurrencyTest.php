<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

beforeEach(function () {

    Cache::flush();

    Route::post('/orders', function () {

        usleep(500000); // Simulate a slow request (0.5 second)

        return response()->json([
            'created' => true,
        ]);

    })->middleware('idempotency');

});

it('handles multiple sequential requests using the same idempotency key', function () {

    $headers = [
        'Idempotency-Key' => 'concurrent-key',
    ];

    $responses = [];

    for ($i = 0; $i < 10; $i++) {

        $responses[] = $this->postJson(
            '/orders',
            [],
            $headers
        );

    }

    foreach ($responses as $response) {
        $response->assertOk();
    }

    expect(
        collect($responses)
            ->map(fn ($response) => $response->json())
            ->unique()
            ->count()
    )->toBe(1);

});