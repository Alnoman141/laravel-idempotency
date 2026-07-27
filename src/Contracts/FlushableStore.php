<?php

namespace Alnoman141\LaravelIdempotency\Contracts;

interface FlushableStore
{
    public function flush(): void;
}