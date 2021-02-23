<?php declare(strict_types=1);

namespace App\Http\Transformers;

use App\Models\Wallet\Balance;

class BalanceTransformer extends AbstractTransformer
{
    protected function attributes(Balance $balance): array
    {
        return [
            'account' => $balance->account,
            'amount' => $balance->amount,
            'currency' => $balance->currency,
        ];
    }
}
