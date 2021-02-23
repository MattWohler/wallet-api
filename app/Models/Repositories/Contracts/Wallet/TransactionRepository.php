<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts\Wallet;

use App\Exceptions\Handled\WalletServiceException;
use App\Models\Wallet\Transaction;

interface TransactionRepository
{
    /**
     * Create a transaction
     *
     * @param  array  $data
     * @return Transaction
     * @throws WalletServiceException
     */
    public function create(array $data): Transaction;
}
