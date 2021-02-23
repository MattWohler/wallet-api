<?php

namespace Tests\Fakes\Repositories;

use App\Models\Repositories\Contracts\Wallet\BalanceRepository;
use App\Models\Wallet\Balance;

class FakeBalanceRepository implements BalanceRepository
{
    public function getBalance(string $account): Balance
    {
        return factory(Balance::class)->make([
            'account' => $account,
            'amount' => 2501.67,
            'currency' => 'USD',
        ]);
    }
}
