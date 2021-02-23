<?php declare(strict_types=1);

namespace App\Models\Repositories\Wallet;

use App\Models\Repositories\Contracts\Wallet\FigureRepository as Repository;
use App\Models\Wallet\Figure;
use Carbon\Carbon;

class FigureRepository extends AbstractRepository implements Repository
{
    public function getFigure(string $account, Carbon $startDate, Carbon $endDate): Figure
    {
        $data = $this->wallet->getFigureByRange($account, $startDate, $endDate);
        return new Figure($data);
    }
}
