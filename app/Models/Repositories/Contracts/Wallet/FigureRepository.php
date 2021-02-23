<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts\Wallet;

use App\Exceptions\Handled\WalletServiceException;
use App\Models\Wallet\Figure;
use Carbon\Carbon;

interface FigureRepository
{
    /**
     * Get figure by account, start and end date
     *
     * @param  string  $account
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @return Figure
     * @throws WalletServiceException
     */
    public function getFigure(string $account, Carbon $startDate, Carbon $endDate): Figure;
}
