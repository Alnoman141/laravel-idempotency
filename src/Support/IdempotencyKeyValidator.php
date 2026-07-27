<?php

namespace Alnoman141\LaravelIdempotency\Support;

use Alnoman141\LaravelIdempotency\Exceptions\InvalidIdempotencyKeyException;

class IdempotencyKeyValidator
{
    public function validate(string $key): void
{
    $key = trim($key);

    if ($key === '') {
        throw new InvalidIdempotencyKeyException(
            'The idempotency key cannot be empty.'
        );
    }

    if (strlen($key) > config('idempotency.key.max_length', 255)) {
        throw new InvalidIdempotencyKeyException(
            'The idempotency key exceeds the maximum allowed length.'
        );
    }

    if (! preg_match(config('idempotency.key.pattern'), $key)) {
        throw new InvalidIdempotencyKeyException(
            'The idempotency key contains invalid characters.'
        );
    }
}
}
