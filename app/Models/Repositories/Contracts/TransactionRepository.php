<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

interface TransactionRepository
{
    /**
     * Find a transaction by id
     *
     * @param  integer  $id
     * @return Transaction
     */
    public function find(int $id): Transaction;

    /**
     * Find a transaction by provider and transaction id
     *
     * @param  int  $providerId
     * @param  string  $providerTransactionId
     * @return Transaction|null
     * @throws ModelNotFoundException
     */
    public function findByProviderIdAndTransactionId(int $providerId, string $providerTransactionId): ?Transaction;

    /**
     * Find transactions by criteria
     *
     * @param  array  $criteria
     * @return Collection
     */
    public function findAll(array $criteria): Collection;

    /**
     * Create a transaction
     *
     * @param  array  $data
     * @return Transaction
     */
    public function create(array $data): Transaction;
}
