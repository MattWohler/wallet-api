<?php

namespace Tests\Unit\Repositories;

use App\Models\Repositories\Contracts\Wallet\PlayerRepository;
use App\Models\Wallet\Player;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Illuminate\Contracts\Cache\Repository as Cache;
use Mockery;
use Tests\TestCase;

class PlayerRepositoryTest extends TestCase
{
    public function test_can_get_player_from_dgs_with_balance()
    {
        $account = $this->faker->uuid;
        $amount = $this->faker->randomFloat();
        $data = $this->buildData($account);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('authenticate')
            ->with($account)
            ->once()
            ->andReturn($data);

        $wallet->shouldReceive('getBalance')
            ->with($account)
            ->once()
            ->andReturn(['amount' => $amount, 'currency' => $data['currency']]);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $player = $repository->getPlayer($account);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals($account, $player->account);
        $this->assertEquals($amount, $player->balance);
    }

    public function test_can_get_player_from_dgs_without_balance()
    {
        $account = $this->faker->uuid;
        $data = $this->buildData($account);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldNotReceive('getBalance');
        $wallet->shouldReceive('authenticate')
            ->with($account)
            ->once()
            ->andReturn($data);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $player = $repository->getPlayer($account, false);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals($account, $player->account);
        $this->assertNull($player->balance);
    }

    public function test_can_get_player_from_cache_with_balance()
    {
        $account = $this->faker->uuid;
        $amount = $this->faker->randomFloat();
        $data = $this->buildData($account);

        $cache = $this->app->make(Cache::class);
        $cache->put('player:'.$account, $data, 3600);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldNotReceive('authenticate');
        $wallet->shouldReceive('getBalance')
            ->with($account)
            ->once()
            ->andReturn(['amount' => $amount, 'currency' => $data['currency']]);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $player = $repository->getPlayer($account);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals($account, $player->account);
        $this->assertEquals($amount, $player->balance);
    }

    public function test_can_get_player_from_cache_without_balance()
    {
        $account = $this->faker->uuid;
        $data = $this->buildData($account);

        $cache = $this->app->make(Cache::class);
        $cache->put('player:'.$account, $data, 3600);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldNotReceive('authenticate');
        $wallet->shouldNotReceive('getBalance');

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $player = $repository->getPlayer($account, false);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals($account, $player->account);
        $this->assertNull($player->balance);
    }

    public function test_can_get_players_from_cache()
    {
        $accounts = [$this->faker->uuid, $this->faker->uuid];
        $data = $this->buildData($accounts);

        $cache = $this->app->make(Cache::class);
        $cache->put('players:'.implode(':', $accounts), $data, 3600);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldNotReceive('getPlayersByAccounts');

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $players = $repository->getPlayers($accounts);
        $this->assertCount(2, $players);

        $players->each(function ($player) use ($accounts) {
            $this->assertInstanceOf(Player::class, $player);
            $this->assertContains($player->account, $accounts);
        });
    }

    public function test_can_get_players_from_dgs()
    {
        $accounts = [$this->faker->uuid, $this->faker->uuid];
        $data = $this->buildData($accounts);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getPlayersByAccounts')
            ->with($accounts)
            ->once()
            ->andReturn($data);

        $this->app->instance(Wallet::class, $wallet);
        $repository = $this->app->make(PlayerRepository::class);

        $players = $repository->getPlayers($accounts);
        $this->assertCount(2, $players);

        $players->each(function ($player) use ($accounts) {
            $this->assertInstanceOf(Player::class, $player);
            $this->assertContains($player->account, $accounts);
        });
    }

    private function buildData($accounts)
    {
        if (!is_array($accounts)) {
            return $this->buildAccountData($accounts);
        }

        return collect($accounts)->map(function ($account) {
            return $this->buildAccountData($account);
        })->all();
    }

    private function buildAccountData($account)
    {
        return [
            'id' => $this->faker->randomDigit,
            'account' => $account,
            'title' => $this->faker->title,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'brand' => $this->faker->word,
            'brandId' => $this->faker->randomDigit,
            'balance' => null,
            'currency' => $this->faker->currencyCode,
            'country' => $this->faker->countryCode,
            'enableCasino' => $this->faker->boolean,
            'enableCards' => $this->faker->boolean,
            'enableHorses' => $this->faker->boolean,
            'enableSports' => $this->faker->boolean,
            'isTestAccount' => $this->faker->boolean
        ];
    }
}
