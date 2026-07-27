<?php

namespace alnoman141\LaravelIdempotency\Console;

use Illuminate\Console\Command;
use alnoman141\LaravelIdempotency\Models\IdempotencyRecordModel;
use alnoman141\LaravelIdempotency\Enums\IdempotencyStatus;

class StatsCommand extends Command
{
    protected $signature = 'idempotency:stats';

    protected $description = 'Show a summary of stored idempotency records (database driver only)';

    public function handle(): int
    {
        if (config('idempotency.driver') !== 'database') {
            $this->components->info(
                'Stats are only available on the database driver. The cache driver does not track counts.'
            );

            return self::SUCCESS;
        }

        $total = IdempotencyRecordModel::query()->count();
        $expired = IdempotencyRecordModel::query()
            ->where('expires_at', '<=', now())
            ->count();

        $byStatus = IdempotencyRecordModel::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $this->components->twoColumnDetail('Total records', (string) $total);
        $this->components->twoColumnDetail('Expired (awaiting prune)', (string) $expired);

        foreach (IdempotencyStatus::cases() as $status) {
            $this->components->twoColumnDetail(
                ucfirst($status->value),
                (string) ($byStatus[$status->value] ?? 0)
            );
        }

        return self::SUCCESS;
    }
}
