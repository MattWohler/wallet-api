<?php declare(strict_types=1);

namespace App\Http\Transformers;

use App\Models\Wallet\Figure;

class FigureTransformer extends AbstractTransformer
{
    protected function attributes(Figure $figure): array
    {
        return [
            'account' => $figure->account,
            'startDate' => $figure->startDate->format(self::FORMAT_DATE_HOUR),
            'endDate' => $figure->endDate->format(self::FORMAT_DATE_HOUR),
            'loseAmount' => $figure->loseAmount,
            'winAmount' => $figure->winAmount,
            'currency' => $figure->currency,
        ];
    }
}
