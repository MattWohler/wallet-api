<?php

namespace Tests\Fakes\Repositories;

use App\Models\Repositories\Contracts\Wallet\FigureRepository;
use App\Models\Wallet\Figure;
use Carbon\Carbon;

class FakeFigureRepository implements FigureRepository
{
    public function getFigure(string $account, Carbon $startDate, Carbon $endDate): Figure
    {
        return factory(Figure::class)->make([
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'loseAmount' => 501.67,
            'winAmount' => 2501.67,
            'currency' => 'USD',
        ]);
    }
}
