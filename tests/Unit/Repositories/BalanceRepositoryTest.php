<?php

namespace Tests\Unit\Repositories;

use App\Models\Repositories\Contracts\Wallet\BalanceRepository;
use App\Models\Wallet\Balance;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Mockery;
use Tests\TestCase;

class BalanceRepositoryTest extends TestCase
{
    public function test_can_get_balance()
    {
        $account = $this->faker->uuid;
        $amount = $this->faker->randomFloat();

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getBalance')
            ->with($account)
            ->once()
            ->andReturn(['account' => $account, 'amount' => $amount, 'currency' => 'USD']);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(BalanceRepository::class);

        $balance = $repository->getBalance($account);
        $this->assertInstanceOf(Balance::class, $balance);

        $this->assertEquals($account, $balance->account);
        $this->assertEquals($amount, $balance->amount);
    }
}
