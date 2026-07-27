<?php

namespace alnoman141\LaravelIdempotency\Data;

use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;

final readonly class IdempotencyRecord
{
    public function __construct(
        public string $fingerprint,

        public IdempotencyStatus $status,

        public ?int $statusCode = null,

        public array $headers = [],

        public ?string $body = null,
    ) {}

    public function toArray(): array
    {
        return [

            'fingerprint' => $this->fingerprint,

            'status' => $this->status->value,

            'status_code' => $this->statusCode,

            'headers' => $this->headers,

            'body' => $this->body,

        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(

            fingerprint: $data['fingerprint'],

            status: IdempotencyStatus::from($data['status']),

            statusCode: $data['status_code'],

            headers: $data['headers'],

            body: $data['body'],

        );
    }
}
