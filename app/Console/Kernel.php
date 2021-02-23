<?php declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\ConfigCheckCommand;
use App\Console\Commands\DeactivateApiTokenCommand;
use App\Console\Commands\GenerateApiTokenCommand;
use App\Console\Commands\SmartRetryCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** @var array - The Artisan commands provided by your application */
    protected $commands = [
        ConfigCheckCommand::class,
        SmartRetryCommand::class,
        DeactivateApiTokenCommand::class,
        GenerateApiTokenCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        //
    }

    public function bootstrap(): void
    {
        //
    }
}
