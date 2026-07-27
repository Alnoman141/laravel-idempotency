<?php

namespace alnoman141\LaravelIdempotency\Support;

class KeyHasher
{
    public function hash(string $key): string
    {
        return hash('sha256', $key);
    }
}
