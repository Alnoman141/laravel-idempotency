<?php

use alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;

it('converts to and from array', function () {

    $record = new IdempotencyRecord(
        fingerprint: 'abc',
        status: IdempotencyStatus::Completed,
        statusCode: 201,
        headers: ['Content-Type' => ['application/json']],
        body: '{"ok":true}',
    );

    $array = $record->toArray();

    $copy = IdempotencyRecord::fromArray($array);

    expect($copy->fingerprint)->toBe('abc');
    expect($copy->status)->toBe(IdempotencyStatus::Completed);
    expect($copy->statusCode)->toBe(201);
    expect($copy->body)->toBe('{"ok":true}');
});
