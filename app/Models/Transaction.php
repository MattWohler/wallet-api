<?php declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $wallet_transaction_id
 * @property float $old_balance
 * @property float $new_balance
 * @property string $account
 * @property string $provider_transaction_id
 * @property string $round_id
 * @property float $amount
 * @property string $type
 * @property int $provider_id
 * @property string $provider_game_id
 * @property string $payload
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Transaction extends Model
{
    /** @var array */
    protected $fillable = [
        'wallet_transaction_id',
        'old_balance',
        'new_balance',
        'account',
        'provider_transaction_id',
        'round_id',
        'amount',
        'type',
        'provider_id',
        'provider_game_id',
        'payload',
    ];

    /** @var array - The attributes that should be casted to native types. */
    protected $casts = [
        'id' => 'integer',
        'wallet_transaction_id' => 'integer',
        'provider_id' => 'integer',
        'old_balance' => 'float',
        'new_balance' => 'float',
        'amount' => 'float',
    ];
}
