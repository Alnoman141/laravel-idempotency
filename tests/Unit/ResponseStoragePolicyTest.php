<?php

use Illuminate\Http\Response;
use Alnoman141\LaravelIdempotency\Support\ResponseStoragePolicy;

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