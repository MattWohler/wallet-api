<?php declare(strict_types=1);

namespace App\Models\Repositories\Wallet;

use App\Services\Wallet\Contracts\ServiceInterface as Wallet;

abstract class AbstractRepository
{
    /** @var Wallet */
    protected $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }
}
