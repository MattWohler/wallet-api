<?php

namespace Tests\Unit\Transformers;

use App\Http\Transformers\FigureTransformer;
use App\Models\Wallet\Figure;
use Tests\TestCase;

class FigureTransformerTest extends TestCase
{
    public function test_figure_can_be_transformed()
    {
        $figure = factory(Figure::class)->make();
        $data = app(FigureTransformer::class)->transform($figure);

        $this->assertEquals([
            'data' => [
                'type' => 'figure',
                'attributes' => [
                    'account' => $figure->account,
                    'startDate' => $figure->startDate->format(FigureTransformer::FORMAT_DATE_HOUR),
                    'endDate' => $figure->endDate->format(FigureTransformer::FORMAT_DATE_HOUR),
                    'loseAmount' => $figure->loseAmount,
                    'winAmount' => $figure->winAmount,
                    'currency' => $figure->currency,
                ]
            ]
        ], $data);
    }
}
