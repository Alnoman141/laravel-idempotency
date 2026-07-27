<?php

namespace alnoman141\LaravelIdempotency\Contracts;

interface FlushableStore
{
    public function flush(): void;
}
