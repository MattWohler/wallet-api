<?php declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Transaction;

interface TransactionService
{
    /**
     * Get transaction data
     *
     * @param  integer  $providerId
     * @param  string  $providerTransactionId
     *
     * @return array
     */
    public function getTransactionData(int $providerId, string $providerTransactionId): array;

    /**
     * Get a transaction
     *
     * @param  int  $providerId
     * @param  string  $providerTransactionId
     * @return Transaction|null
     */
    public function getTransaction(int $providerId, string $providerTransactionId): ?Transaction;
}
