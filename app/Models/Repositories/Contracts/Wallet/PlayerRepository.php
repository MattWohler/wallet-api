<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts\Wallet;

use App\Models\Wallet\Player;
use Illuminate\Support\Collection;

interface PlayerRepository
{
    /**
     * Get player by account
     *
     * @param  string  $account
     * @param  bool  $withBalance  - if should include the balance
     * @return Player
     */
    public function getPlayer(string $account, bool $withBalance = true): Player;

    /**
     * Get players by batch for accounts
     *
     * @param  string[]  $accounts
     * @return Collection
     */
    public function getPlayers(array $accounts): Collection;
}
