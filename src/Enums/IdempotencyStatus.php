<?php

namespace Alnoman141\LaravelIdempotency\Enums;

enum IdempotencyStatus: string
{
    case Processing = 'processing';

    case Completed = 'completed';

    case Failed = 'failed';
}