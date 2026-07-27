<?php

namespace alnoman141\LaravelIdempotency\Stores;

use alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use alnoman141\LaravelIdempotency\Data\IdempotencyRecord;
use alnoman141\LaravelIdempotency\Models\IdempotencyRecordModel;
use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;
use alnoman141\LaravelIdempotency\Contracts\FlushableStore;

class DatabaseStore implements IdempotencyStore, FlushableStore
{
    protected function hash(string $key): string
    {
        return hash('sha256', $key);
    }

    public function flush(): void
    {
        IdempotencyRecordModel::query()->delete();
    }

    public function find(string $key): ?IdempotencyRecord
    {
        $record = IdempotencyRecordModel::query()
            ->where('key_hash', $this->hash($key))
            ->first();

        if (!$record) {
            return null;
        }

        if ($record->expires_at !== null && $record->expires_at->isPast()) {
            $record->delete();

            return null;
        }

        return new IdempotencyRecord(
            fingerprint: $record->fingerprint,
            status: IdempotencyStatus::from($record->status),
            statusCode: $record->status_code,
            headers: $record->headers ?? [],
            body: $record->body
        );
    }

    public function put(
        string $key,
        IdempotencyRecord $record,
        int $ttl
    ): void {

        IdempotencyRecordModel::updateOrCreate(

            [
                'key_hash' => $this->hash($key),
            ],

            [
                'fingerprint' => $record->fingerprint,
                'status' => $record->status->value,
                'status_code' => $record->statusCode,
                'headers' => $record->headers,
                'body' => $record->body,
                'expires_at' => now()->addSeconds($ttl),
            ]

        );
    }

    public function forget(string $key): void
    {
        IdempotencyRecordModel::query()
            ->where('key_hash', $this->hash($key))
            ->delete();
    }
}
