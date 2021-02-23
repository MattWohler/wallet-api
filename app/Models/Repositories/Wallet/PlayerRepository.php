<?php declare(strict_types=1);

namespace App\Models\Repositories\Wallet;

use App\Models\Repositories\Contracts\Wallet\PlayerRepository as Repository;
use App\Models\Wallet\Player;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

class PlayerRepository extends AbstractRepository implements Repository
{
    /** @var int - 1 day in seconds */
    protected const DEFAULT_TTL = 86400;

    /** @var Cache */
    protected $cache;

    public function __construct(Wallet $wallet, Cache $cache)
    {
        parent::__construct($wallet);
        $this->cache = $cache;
    }

    public function getPlayer(string $account, bool $withBalance = true): Player
    {
        $data = $this->fetchAuthenticatedPlayer($account);

        if ($withBalance) {
            $balance = $this->wallet->getBalance($account);
            $data['balance'] = $balance['amount'];
            $data['currency'] = $balance['currency'];
        }

        return new Player($data);
    }

    protected function fetchAuthenticatedPlayer(string $account): array
    {
        $key = 'player:'.$account;

        return $this->cache->remember($key, self::DEFAULT_TTL, function () use ($account) {
            return $this->wallet->authenticate($account);
        });
    }

    public function getPlayers(array $accounts): Collection
    {
        $data = $this->fetchPlayersByAccounts(array_unique($accounts));

        return collect($data)->map(static function ($datum) {
            return new Player($datum);
        });
    }

    protected function fetchPlayersByAccounts(array $accounts): array
    {
        $key = 'players:'.implode(':', $accounts);

        return $this->cache->remember($key, self::DEFAULT_TTL, function () use ($accounts) {
            return $this->wallet->getPlayersByAccounts($accounts);
        });
    }
}
