<?php declare(strict_types=1);

namespace App\Models\Wallet;

use Jenssegers\Model\Model;

/**
 * @property string $id
 * @property string $type
 * @property string $account
 * @property float $amount
 * @property float $previousBalance
 * @property float $currentBalance
 * @property string $currency
 */
class Transaction extends Model
{
    /** @var array */
    protected $fillable = [
        'id',
        'type',
        'account',
        'amount',
        'previousBalance',
        'currentBalance',
        'currency',
    ];

    public function getTransactionJobData(array $data = []): array
    {
        return array_merge($data, [
            'walletTransactionId' => $this->id,
            'oldBalance' => $this->previousBalance,
            'newBalance' => $this->currentBalance,
            'currency' => $this->currency
        ]);
    }
}
