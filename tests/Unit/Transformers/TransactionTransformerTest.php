<?php

namespace Tests\Unit\Transformers;

use App\Http\Transformers\TransactionTransformer;
use App\Models\Wallet\Transaction;
use Tests\TestCase;

class TransactionTransformerTest extends TestCase
{
    public function test_transaction_can_be_transformed()
    {
        $transaction = factory(Transaction::class)->make();
        $data = app(TransactionTransformer::class)->transform($transaction);

        $this->assertEquals([
            'data' => [
                'type' => 'transaction',
                'id' => (string) $transaction->id,
                'attributes' => [
                    'type' => $transaction->type,
                    'account' => $transaction->account,
                    'amount' => $transaction->amount,
                    'previousBalance' => $transaction->previousBalance,
                    'currentBalance' => $transaction->currentBalance,
                    'currency' => $transaction->currency,
                ]
            ]
        ], $data);
    }
}
