<?php declare(strict_types=1);

namespace App\Models\Repositories\Wallet;

use App\Models\Repositories\Contracts\Wallet\BalanceRepository as Repository;
use App\Models\Wallet\Balance;

class BalanceRepository extends AbstractRepository implements Repository
{
    public function getBalance(string $account): Balance
    {
        $data = $this->wallet->getBalance($account);
        return new Balance($data);
    }
}
