<?php

namespace Alnoman141\LaravelIdempotency\Console;

use Illuminate\Console\Command;
use Alnoman141\LaravelIdempotency\Contracts\FlushableStore;

class ClearIdempotencyCommand extends Command
{
    protected $signature = 'idempotency:clear';

    protected $description = 'Clear all stored idempotency records';

    public function __construct(
        private readonly FlushableStore $store
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->store->flush();

        $this->components->info(
            'All idempotency records have been cleared.'
        );

        return self::SUCCESS;
    }
}