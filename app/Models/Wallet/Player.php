<?php declare(strict_types=1);

namespace App\Models\Wallet;

use Jenssegers\Model\Model;

/**
 * @property int $id
 * @property string $account
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $brand
 * @property int $brandId
 * @property float $balance
 * @property string $currency
 * @property string $country
 * @property bool $enableCasino
 * @property bool $enableCards
 * @property bool $enableHorses
 * @property bool $enableSports
 * @property bool $isTestAccount
 */
class Player extends Model
{
    /** @var array */
    protected $fillable = [
        'id', // dgs IdPlayer
        'account',
        'title',
        'firstName',
        'lastName',
        'brand',
        'brandId',
        'balance',
        'currency',
        'country',
        'enableCasino',
        'enableCards',
        'enableHorses',
        'enableSports',
        'isTestAccount'
    ];
}
