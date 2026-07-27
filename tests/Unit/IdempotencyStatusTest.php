<?php

use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;

it('has processing status', function () {
    expect(IdempotencyStatus::Processing->value)
        ->toBe('processing');
});

it('has completed status', function () {
    expect(IdempotencyStatus::Completed->value)
        ->toBe('completed');
});

it('has failed status', function () {
    expect(IdempotencyStatus::Failed->value)
        ->toBe('failed');
});
