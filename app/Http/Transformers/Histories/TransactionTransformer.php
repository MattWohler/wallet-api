<?php declare(strict_types=1);

namespace App\Http\Transformers\Histories;

use App\Http\Transformers\AbstractTransformer;
use App\Models\Transaction;

class TransactionTransformer extends AbstractTransformer
{
    protected function attributes(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'oldBalance' => $transaction->old_balance,
            'newBalance' => $transaction->new_balance,
            'account' => $transaction->account,
            'providerTransactionId' => $transaction->provider_transaction_id,
            'roundId' => $transaction->round_id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'providerId' => $transaction->provider_id,
            'providerGameId' => $transaction->provider_game_id,
            'createdAt' => $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
