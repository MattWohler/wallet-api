<?php declare(strict_types=1);

namespace App\Services\Wallet\Illuminate;

use App\Services\Wallet\Contracts\ServiceInterface as WalletService;
use Illuminate\Support\Manager;

class WalletManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->app['config']['wallet.default'];
    }

    public function createDgsDriver(): WalletService
    {
        $config = $this->app['config']['wallet.services.dgs'];
        return $this->app->make($config['class']);
    }
}
