<?php

namespace Alnoman141\LaravelIdempotency\Support;

class KeyHasher
{
    public function hash(string $key): string
    {
        return hash('sha256', $key);
    }
}