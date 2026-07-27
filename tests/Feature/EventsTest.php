<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use alnoman141\LaravelIdempotency\Events\IdempotencyStarted;
use alnoman141\LaravelIdempotency\Events\IdempotencyCompleted;

beforeEach(function () {

    Route::post('/orders', function () {
        return response()->json([
            'created' => true,
        ]);
    })->middleware('idempotency');
});

it('dispatches started and completed events', function () {

    Event::fake();

    $this->postJson(
        '/orders',
        [],
        [
            'Idempotency-Key' => 'abc',
        ]
    )->assertOk();

    Event::assertDispatched(IdempotencyStarted::class);

    Event::assertDispatched(IdempotencyCompleted::class);
});
