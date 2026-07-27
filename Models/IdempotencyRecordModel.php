<?php

namespace Alnoman141\LaravelIdempotency\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyRecordModel extends Model
{
    protected $table = 'idempotency_records';

    protected $fillable = [

        'key_hash',

        'fingerprint',

        'status',

        'status_code',

        'headers',

        'body',

        'expires_at',

    ];

    protected $casts = [

        'headers' => 'array',

        'expires_at' => 'datetime',

    ];
}