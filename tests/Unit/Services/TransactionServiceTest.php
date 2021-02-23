<?php declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Repositories\Contracts\TransactionRepository;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Contracts\Cache\Repository as Cache;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    /** @var MockInterface */
    private $cache;

    /** @var MockInterface */
    private $repository;

    /** @var TransactionService */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = Mockery::mock(Cache::class);
        $this->repository = Mockery::mock(TransactionRepository::class);

        $this->app->instance(Cache::class, $this->cache);
        $this->app->instance(TransactionRepository::class, $this->repository);

        $this->service = $this->app->make(TransactionService::class);
    }

    public function test_cannot_get_nonexistant_transaction()
    {
        $providerId = 1;
        $providerTransactionId = '06213511';
        $key = 'saved::transaction:'.$providerId.':'.$providerTransactionId;

        $this->cache->shouldReceive('get')->with($key)->once()->andReturnNull();

        $this->cache->shouldReceive('put')
            ->with($key, null, 60 * 60)
            ->once()
            ->andReturn();

        $this->repository->shouldReceive('findByProviderIdAndTransactionId')
            ->with($providerId, $providerTransactionId)
            ->once()
            ->andReturnNull();

        $this->assertEquals([], $this->service->getTransactionData($providerId, $providerTransactionId));
    }

    public function test_can_get_transaction_data_from_database()
    {
        $providerId = 1;
        $providerTransactionId = '06213511';
        $key = 'saved::transaction:'.$providerId.':'.$providerTransactionId;

        $this->cache->shouldReceive('get')->with($key)->once()->andReturnNull();
        $transaction = factory(Transaction::class)->make(['payload' => json_encode(['data'])]);

        $this->repository->shouldReceive('findByProviderIdAndTransactionId')
            ->with($providerId, $providerTransactionId)
            ->once()
            ->andReturn($transaction);

        $this->cache->shouldReceive('put')
            ->with($key, $transaction, 60 * 60)
            ->once()
            ->andReturn();

        $this->assertEquals(['data'], $this->service->getTransactionData($providerId, $providerTransactionId));
    }

    public function test_can_get_transaction_data_from_cache()
    {
        $providerId = 1;
        $providerTransactionId = '06213511';
        $key = 'saved::transaction:'.$providerId.':'.$providerTransactionId;
        $transaction = factory(Transaction::class)->make(['payload' => json_encode(['data'])]);

        $this->cache->shouldReceive('get')
            ->with($key)
            ->once()
            ->andReturn($transaction);

        $this->cache->shouldNotReceive('put');
        $this->repository->shouldNotReceive('findByProviderIdAndTransactionId');

        $this->assertEquals(['data'], $this->service->getTransactionData($providerId, $providerTransactionId));
    }
}
