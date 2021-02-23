<?php

namespace Tests\Feature\Repositories;

use App\Models\Repositories\Contracts\TransactionRepository;
use App\Models\Transaction;
use Carbon\Carbon;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    /** @var TransactionRepository */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(TransactionRepository::class);
    }

    public function test_can_find_transaction()
    {
        $transaction = factory(Transaction::class)->create();
        $found = $this->repository->find($transaction->id);

        $this->assertEquals($transaction->id, $found->id);
    }

    public function test_can_create_transaction()
    {
        $data = [
            'walletTransactionId' => 1231,
            'oldBalance' => 1235.21,
            'newBalance' => 552.15,
            'account' => 'MB4017',
            'providerTransactionId' => 'BS1244',
            'roundId' => '21211',
            'amount' => 1531.35,
            'type' => 'bet',
            'providerId' => 1,
            'providerGameId' => '1531',
        ];

        $transaction = $this->repository->create($data);

        $this->assertNotNull($transaction->id);
        $this->assertEquals($transaction->wallet_transaction_id, $data['walletTransactionId']);
        $this->assertEquals($transaction->old_balance, $data['oldBalance']);
        $this->assertEquals($transaction->new_balance, $data['newBalance']);
        $this->assertEquals($transaction->account, $data['account']);
        $this->assertEquals($transaction->provider_transaction_id, $data['providerTransactionId']);
        $this->assertEquals($transaction->round_id, $data['roundId']);
        $this->assertEquals($transaction->amount, $data['amount']);
        $this->assertEquals($transaction->type, $data['type']);
        $this->assertEquals($transaction->provider_id, $data['providerId']);
        $this->assertEquals($transaction->provider_game_id, $data['providerGameId']);
        $this->assertEquals($transaction->payload, json_encode($data));
    }

    public function testFindByProviderIdAndTransactionId()
    {
        $providerId = 1;
        $providerTransactionId = '5154';

        $transaction = factory(Transaction::class)->create([
            'provider_id' => $providerId,
            'provider_transaction_id' => $providerTransactionId
        ]);

        factory(Transaction::class)->create([
            'provider_id' => 12,
            'provider_transaction_id' => $providerTransactionId
        ]);

        factory(Transaction::class)->create([
            'provider_id' => $providerId,
            'provider_transaction_id' => 12
        ]);

        factory(Transaction::class)->create([
            'provider_id' => 12,
            'provider_transaction_id' => 12
        ]);

        $found = $this->repository->findByProviderIdAndTransactionId($providerId, $providerTransactionId);

        $this->assertEquals($transaction->id, $found->id);
    }

    public function test_find_all_by_filters_with_all_filters()
    {
        $filters = [
            'account' => 'MB4017',
            'type' => 'bet',
            'providerId' => 1,
            'providerTransactionId' => '1531531',
            'roundId' => '21531',
            'startDate' => Carbon::now(),
            'endDate' => Carbon::now(),
        ];

        $transaction = factory(Transaction::class)->create([
            'account' => $filters['account'],
            'provider_transaction_id' => $filters['providerTransactionId'],
            'round_id' => $filters['roundId'],
            'type' => $filters['type'],
            'provider_id' => $filters['providerId'],
            'created_at' => $filters['startDate'],
        ]);

        factory(Transaction::class)->create();

        $transactions = $this->repository->findAll($filters);

        $this->assertCount(1, $transactions);
        $this->assertEquals($transactions[0]->id, $transaction->id);
    }

    public function test_find_all_by_filters_without_filters()
    {
        factory(Transaction::class, 5)->create();

        $transactions = $this->repository->findAll([]);

        $this->assertCount(5, $transactions);
    }
}
