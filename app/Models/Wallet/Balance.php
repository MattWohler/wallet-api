<?php declare(strict_types=1);

namespace App\Models\Wallet;

use Jenssegers\Model\Model;

/**
 * @property string $account
 * @property float $amount
 * @property string $currency
 */
class Balance extends Model
{
    /** @var array */
    protected $fillable = [
        'account',
        'amount',
        'currency',
    ];
}
