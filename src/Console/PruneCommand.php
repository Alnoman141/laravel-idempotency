<?php

namespace alnoman141\LaravelIdempotency\Console;

use Illuminate\Console\Command;
use alnoman141\LaravelIdempotency\Models\IdempotencyRecordModel;

class PruneCommand extends Command
{
    protected $signature = 'idempotency:prune';

    protected $description = 'Delete expired idempotency records (database driver only)';

    public function handle(): int
    {
        if (config('idempotency.driver') !== 'database') {
            $this->components->info(
                'Nothing to prune: the cache driver expires records via TTL automatically.'
            );

            return self::SUCCESS;
        }

        $deleted = IdempotencyRecordModel::query()
            ->where('expires_at', '<=', now())
            ->delete();

        $this->components->info(
            "Pruned {$deleted} expired idempotency record(s)."
        );

        return self::SUCCESS;
    }
}
