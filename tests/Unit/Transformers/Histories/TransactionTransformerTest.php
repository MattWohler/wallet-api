<?php declare(strict_types=1);

namespace Tests\Unit\Transformers\Histories;

use App\Http\Transformers\Histories\TransactionTransformer;
use App\Models\Transaction;
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
                    'createdAt' => $transaction->created_at,
                ]
            ]
        ], $data);
    }

}
