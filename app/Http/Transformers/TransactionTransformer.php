<?php declare(strict_types=1);

namespace App\Http\Transformers;

use App\Models\Wallet\Transaction;

class TransactionTransformer extends AbstractTransformer
{
    protected function attributes(Transaction $transaction): array
    {
        return [
            'type' => $transaction->type,
            'account' => $transaction->account,
            'amount' => $transaction->amount,
            'previousBalance' => $transaction->previousBalance,
            'currentBalance' => $transaction->currentBalance,
            'currency' => $transaction->currency,
        ];
    }
}
