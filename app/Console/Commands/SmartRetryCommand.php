<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SmartRetryCommand extends Command
{
    /** @var string */
    protected $signature = 'queue:smart-retry';

    /** @var string */
    protected $description = 'Push the failed jobs back into the queue';

    public function handle(): void
    {
        $this->line('<info>Loading failed jobs...</info>');

        $failedJobs = DB::table(config('queue.failed.table'))
            ->groupBy('payload')
            ->get();

        if (count($failedJobs) === 0) {
            $this->line("\n<info>There are no failed jobs.</info>\n");
            return;
        }

        $this->line("\n<info>There are ".count($failedJobs)." failed jobs</info>\n");

        $ids = $failedJobs->pluck('id');
        $this->line('<info>Sending job with following id '.$ids->implode(', ').'</info>');

        $this->call('queue:retry', ['id' => $ids->all()]);
        $this->call('queue:flush');
    }
}
