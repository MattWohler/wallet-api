<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\Repositories\Contracts\TransactionRepository as TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function find(int $id): Transaction
    {
        return Transaction::find($id);
    }

    public function findByProviderIdAndTransactionId(int $providerId, string $providerTransactionId): ?Transaction
    {
        return Transaction::where([
            ['provider_id', '=', $providerId],
            ['provider_transaction_id', '=', $providerTransactionId],
        ])->first();
    }

    public function findAll(array $criteria): Collection
    {
        $query = Transaction::query();

        if (array_has($criteria, 'account')) {
            $query->where('account', '=', array_get($criteria, 'account'));
        }

        if (array_has($criteria, 'type')) {
            $query->where('type', '=', array_get($criteria, 'type'));
        }

        if (array_has($criteria, 'providerId')) {
            $query->where('provider_id', '=', array_get($criteria, 'providerId'));
        }

        if (array_has($criteria, 'providerTransactionId')) {
            $query->where('provider_transaction_id', '=', array_get($criteria, 'providerTransactionId'));
        }

        if (array_has($criteria, 'roundId')) {
            $query->where('round_id', '=', array_get($criteria, 'roundId'));
        }

        if (array_has($criteria, 'startDate')) {
            $query->where('created_at', '>=', array_get($criteria, 'startDate'));
        }

        if (array_has($criteria, 'endDate')) {
            $query->where('created_at', '<=', array_get($criteria, 'endDate'));
        }

        return $query->get();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create([
            'wallet_transaction_id' => $data['walletTransactionId'],
            'old_balance' => $data['oldBalance'],
            'new_balance' => $data['newBalance'],
            'account' => $data['account'],
            'provider_transaction_id' => $data['providerTransactionId'],
            'round_id' => (string) $data['roundId'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'provider_id' => $data['providerId'],
            'provider_game_id' => $data['providerGameId'],
            'payload' => json_encode($data),
        ]);
    }
}
