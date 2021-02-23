<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts\Wallet;

use App\Models\Wallet\Balance;

interface BalanceRepository
{
    /**
     * Get balance by account
     *
     * @param  string  $account
     * @return Balance
     */
    public function getBalance(string $account): Balance;
}
