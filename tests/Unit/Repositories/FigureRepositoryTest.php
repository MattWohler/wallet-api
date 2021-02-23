<?php

namespace Tests\Unit\Repositories;

use App\Models\Repositories\Contracts\Wallet\FigureRepository;
use App\Models\Wallet\Figure;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class FigureRepositoryTest extends TestCase
{
    public function test_can_get_figure()
    {
        $account = $this->faker->uuid;
        $startDate = Carbon::parse($this->faker->date());
        $endDate = Carbon::parse($this->faker->date());
        $amount = $this->faker->randomFloat();

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getFigureByRange')
            ->with($account, $startDate, $endDate)
            ->once()
            ->andReturn([
                'account' => $account,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'loseAmount' => $amount,
                'winAmount' => $amount,
            ]);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(FigureRepository::class);

        $figure = $repository->getFigure($account, $startDate, $endDate);
        $this->assertInstanceOf(Figure::class, $figure);

        $this->assertEquals($account, $figure->account);
        $this->assertEquals($startDate, $figure->startDate);
        $this->assertEquals($endDate, $figure->endDate);
        $this->assertEquals($amount, $figure->loseAmount);
        $this->assertEquals($amount, $figure->winAmount);
    }
}
