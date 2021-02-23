<?php

namespace Tests\Unit\Transformers;

use App\Http\Transformers\BalanceTransformer;
use App\Models\Wallet\Balance;
use Tests\TestCase;

class BalanceTransformerTest extends TestCase
{
    public function test_balance_can_be_transformed()
    {
        $balance = factory(Balance::class)->make();
        $data = app(BalanceTransformer::class)->transform($balance);

        $this->assertEquals([
            'data' => [
                'type' => 'balance',
                'attributes' => [
                    'account' => $balance->account,
                    'amount' => $balance->amount,
                    'currency' => $balance->currency,
                ]
            ]
        ], $data);
    }
}
