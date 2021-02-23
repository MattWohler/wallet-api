<?php declare(strict_types=1);

namespace App\Providers;

use App\Services\Driver\Dgs\DgsResponseParser;
use App\Services\Driver\Dgs\DgsService;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use App\Services\Wallet\Illuminate\WalletManager;
use App\Support\Logger;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DgsService::class, static function ($app) {
            $connection = $app['config']['wallet.services.dgs.config.connection'];

            $database = $app->make(DatabaseManager::class)->connection($connection);
            $response = $app->make(DgsResponseParser::class);
            $logger = $app->make(Logger::class);

            return new DgsService($database, $response, $logger);
        });

        $this->app->singleton(WalletManager::class, static function ($app) {
            return new WalletManager($app);
        });

        $this->app->singleton(Wallet::class, static function ($app) {
            return $app->make(WalletManager::class)->driver();
        });
    }
}
