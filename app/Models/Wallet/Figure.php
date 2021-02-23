<?php declare(strict_types=1);

namespace App\Models\Wallet;

use Carbon\Carbon;
use Jenssegers\Model\Model;

/**
 * @property string $account
 * @property Carbon $startDate
 * @property Carbon $endDate
 * @property float $loseAmount
 * @property float $winAmount
 * @property string $currency
 */
class Figure extends Model
{
    /** @var array */
    protected $fillable = [
        'account',
        'startDate',
        'endDate',
        'loseAmount',
        'winAmount',
        'currency',
    ];
}
