<?php

namespace Tests\Unit\Repositories;

use App\Models\Repositories\Contracts\Wallet\TransactionRepository;
use App\Models\Wallet\Transaction;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Mockery;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    public function test_can_create_transaction_in_wallet()
    {
        $id = $this->faker->randomDigit;
        $account = $this->faker->uuid;
        $amount = $this->faker->randomFloat();
        $balance = $this->faker->randomFloat();

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('insertTransaction')
            ->with([])
            ->once()
            ->andReturn([
                'id' => $id,
                'account' => $account,
                'amount' => $amount,
                'previousBalance' => $balance,
                'currentBalance' => $balance,
            ]);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(TransactionRepository::class);

        $transaction = $repository->create([]);
        $this->assertInstanceOf(Transaction::class, $transaction);

        $this->assertEquals($id, $transaction->id);
        $this->assertEquals($account, $transaction->account);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals($balance, $transaction->previousBalance);
        $this->assertEquals($balance, $transaction->currentBalance);
    }
}
