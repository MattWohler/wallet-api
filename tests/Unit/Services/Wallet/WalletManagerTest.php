<?php

namespace Tests\Unit\Services\Wallet;

use App\Services\Driver\Dgs\DgsService;
use App\Services\Wallet\Contracts\ServiceInterface as WalletService;
use App\Services\Wallet\Illuminate\WalletManager;
use InvalidArgumentException;
use Tests\TestCase;

class WalletManagerTest extends TestCase
{
    public function test_manager_can_create_default_service()
    {
        $manager = $this->app->make(WalletManager::class);

        $service = $manager->driver();
        $this->assertInstanceOf(WalletService::class, $service);

        $service = $manager->driver('dgs');
        $this->assertInstanceOf(WalletService::class, $service);
        $this->assertInstanceOf(DgsService::class, $service);
    }

    public function test_manager_throws_exception_for_unsupported_driver()
    {
        $this->expectException(InvalidArgumentException::class);

        $manager = $this->app->make(WalletManager::class);
        $manager->driver('unknown_service');
    }
}
