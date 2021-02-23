<?php declare(strict_types=1);

namespace App\Models\Repositories\Wallet;

use App\Models\Repositories\Contracts\Wallet\TransactionRepository as Repository;
use App\Models\Wallet\Transaction;

class TransactionRepository extends AbstractRepository implements Repository
{
    public function create(array $data): Transaction
    {
        $attributes = $this->wallet->insertTransaction($data);
        return new Transaction($attributes);
    }
}
