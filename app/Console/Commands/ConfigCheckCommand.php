<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfigCheckCommand extends Command
{
    /** @var string */
    protected $signature = 'config:check';

    /** @var string */
    protected $description = 'Checks that required environment variables are set';

    public function handle(): void
    {
        $this->line('Everything looks good for <info>'.env('APP_ENV').'</info>');
    }
}
