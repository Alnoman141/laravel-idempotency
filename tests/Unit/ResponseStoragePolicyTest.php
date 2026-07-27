<?php

use Illuminate\Http\Response;
use alnoman141\LaravelIdempotency\Support\ResponseStoragePolicy;

it('stores successful responses', function () {

    $policy = new ResponseStoragePolicy();

    expect(
        $policy->shouldStore(
            new Response([], 201)
        )
    )->toBeTrue();
});

it('does not store server errors', function () {

    $policy = new ResponseStoragePolicy();

    expect(
        $policy->shouldStore(
            new Response([], 500)
        )
    )->toBeFalse();
});
