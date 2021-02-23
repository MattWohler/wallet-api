<?php

namespace Tests\Fakes\Repositories;

use App\Models\Repositories\Contracts\Wallet\PlayerRepository;
use App\Models\Wallet\Player;
use Illuminate\Support\Collection;

class FakePlayerRepository implements PlayerRepository
{
    public function getPlayer(string $account, bool $withBalance = true): Player
    {
        $balance = $withBalance ? 2501.67 : null;
        return factory(Player::class)->make([
            'id' => 12,
            'account' => $account,
            'title' => 'Ms',
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'brand' => 'mybookie',
            'brandId' => 35,
            'balance' => $balance,
            'currency' => 'USD',
            'country' => 'USA',
            'enableCasino' => true,
            'enableCards' => true,
            'enableHorses' => true,
            'enableSports' => true,
            'isTestAccount' => true
        ]);
    }

    public function getPlayers(array $accounts): Collection
    {
        $players = collect();

        foreach ($accounts as $account) {
            $players->push($this->getPlayer((string) $account, false));
        }

        return $players;
    }
}
