<?php

namespace Tests\Feature;

use App\Jobs\TransactionJob;
use App\Models\Repositories\Contracts\TransactionRepository;
use App\Services\Contracts\TransactionService;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Illuminate\Auth\GenericUser;
use Mockery;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function test_cannot_process_transaction_without_authorization()
    {
        $this->post('/api/v1/transaction', []);

        $this->assertResponseStatus(401);
    }

    public function test_cannot_process_transaction_without_required_parameters()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', []);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_cannot_process_transaction_without_right_parameter_types()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', [
            'account' => 123, // 'required|string'
            'amount' => 'random_string', // 'required|numeric'
            'brandId' => 'random_string', // 'required|integer'
            'providerId' => 'random_string', // 'required|integer'
            'providerGameId' => 'random_string', // 'required|integer'
            'providerTransactionId' => 'random_string', // 'required|integer'
            'roundId' => 'random_string', // 'required|integer'
            'reference' => 123, // 'required|string'
            'currency' => 123, // 'required|string'
            'description' => 1235, // 'string'
            'type' => 123, // 'required|string'
        ]);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_wont_process_duplicate_transaction()
    {
        $data = $this->getTransactionData();
        $data['walletTransactionId'] = 123;
        $data['oldBalance'] = 456.56;
        $data['newBalance'] = 500.00;

        $repository = $this->app->make(TransactionRepository::class);
        $transaction = $repository->create($data);

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn(json_decode($transaction->payload, true));

        $this->app->instance(TransactionService::class, $transactionService);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', $data);

        $this->assertResponseStatus(409);
        $this->assertResponseMatchesSwagger();
    }

    private function getTransactionData(): array
    {
        return [
            'account' => '12345',
            'amount' => 46.5,
            'brandId' => 12,
            'providerId' => 15,
            'providerGameId' => '13',
            'providerTransactionId' => '16',
            'roundId' => '2',
            'reference' => 'reference',
            'currency' => 'USD',
            'description' => 'string',
            'type' => 'bet',
        ];
    }

    public function test_wont_process_transaction_with_insufficient_funds()
    {
        $data = $this->getTransactionData();
        $data['amount'] *= -1;

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn([]);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getBalance')
            ->with($data['account'])
            ->once()
            ->andReturn(['amount' => (float) 0, 'currency' => 'USD', 'account' => $data['account']]);

        $this->app->instance(TransactionService::class, $transactionService);
        $this->app->instance(Wallet::class, $wallet);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', $data);

        $this->assertResponseStatus(403);
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_process_transaction()
    {
        $this->fakeJobs();
        $data = $this->getTransactionData();

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn([]);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getBalance')
            ->with($data['account'])
            ->once()
            ->andReturn(['amount' => $data['amount'] + 50, 'currency' => 'USD', 'account' => $data['account']]);

        $wallet->shouldReceive('insertTransaction')
            ->with(Mockery::subset($data))
            ->once()
            ->andReturn([
                'id' => 12223,
                'type' => $data['type'],
                'account' => $data['account'],
                'amount' => (float) $data['amount'],
                'previousBalance' => (float) $data['amount'] + 500,
                'currentBalance' => (float) 500,
                'currency' => 'USD',
            ]);

        $this->app->instance(TransactionService::class, $transactionService);
        $this->app->instance(Wallet::class, $wallet);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', $data);

        $this->assertResponseStatus(200);
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('transaction', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
        $this->assertJobsDispatched(TransactionJob::class);
    }

    public function test_can_process_transaction_when_balance_equals_amount()
    {
        $this->fakeJobs();
        $data = $this->getTransactionData();

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn([]);

        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldReceive('getBalance')
            ->with($data['account'])
            ->once()
            ->andReturn(['amount' => $data['amount'], 'currency' => 'USD', 'account' => $data['account']]);

        $wallet->shouldReceive('insertTransaction')
            ->with(Mockery::subset($data))
            ->once()
            ->andReturn([
                'id' => 12223,
                'type' => $data['type'],
                'account' => $data['account'],
                'amount' => (float) $data['amount'],
                'previousBalance' => (float) $data['amount'],
                'currentBalance' => (float) 500,
                'currency' => 'USD',
            ]);

        $this->app->instance(TransactionService::class, $transactionService);
        $this->app->instance(Wallet::class, $wallet);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/transaction', $data);

        $this->assertResponseStatus(200);
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('transaction', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
        $this->assertJobsDispatched(TransactionJob::class);
    }
}
