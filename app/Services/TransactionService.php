<?php declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\Contracts\TransactionRepository;
use App\Models\Transaction;
use App\Services\Contracts\TransactionService as TransactionServiceContract;
use Closure;
use Exception;
use Illuminate\Contracts\Cache\Repository as Cache;

class TransactionService implements TransactionServiceContract
{
    /** @var int - an hour in seconds */
    protected const HOUR_TTL = 3600;

    /** @var Cache */
    protected $cache;

    /** @var TransactionRepository */
    protected $repository;

    public function __construct(Cache $cache, TransactionRepository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    public function getTransactionData(int $providerId, string $providerTransactionId): array
    {
        $transaction = $this->getTransaction($providerId, $providerTransactionId);
        return ($transaction === null) ? [] : json_decode($transaction->payload, true);
    }

    public function getTransaction(int $providerId, string $providerTransactionId): ?Transaction
    {
        $key = 'saved::transaction:'.$providerId.':'.$providerTransactionId;

        return $this->remember($key, self::HOUR_TTL, function () use ($providerId, $providerTransactionId) {
            return $this->repository->findByProviderIdAndTransactionId($providerId, $providerTransactionId);
        });
    }

    protected function remember(string $key, int $ttl, Closure $callback): ?Transaction
    {
        try {
            if (($value = $this->cache->get($key)) !== null) {
                return $value;
            }

            $this->cache->put($key, $value = $callback(), $ttl);
            return $value;
        } catch (Exception $e) {
            return $callback();
        }
    }
}
