<?php

namespace Tests\Feature;

use App\Jobs\TransactionJob;
use App\Models\Repositories\Contracts\Wallet\TransactionRepository;
use App\Models\Wallet\Transaction as TransactionWallet;
use App\Services\Contracts\TransactionService;
use Illuminate\Auth\GenericUser;
use Mockery;
use Tests\TestCase;

class RollbackTest extends TestCase
{
    public function test_cannot_process_rollback_without_authorization()
    {
        $this->post('/api/v1/rollback', []);

        $this->assertResponseStatus(401);
    }

    public function test_cannot_process_rollback_without_required_parameters()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/rollback', []);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_cannot_process_rollback_without_right_parameter_types()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/rollback', [
            'account' => 123, // 'required|string'
            'providerId' => 'random_string', // 'required|integer'
            'originalProviderTransactionId' => true, // 'required|string'
            'providerTransactionId' => true, // 'required|string'
        ]);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_wont_process_duplicate_transaction()
    {
        $data = $this->getRollbackData();

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn(['transactionData']);

        $this->app->instance(TransactionService::class, $transactionService);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/rollback', $data);

        $this->assertResponseStatus(409);
        $this->assertResponseMatchesSwagger();
    }

    private function getRollbackData(): array
    {
        return [
            'account' => '12345',
            'providerId' => 15,
            'originalProviderTransactionId' => '10',
            'providerTransactionId' => '16',
        ];
    }

    public function test_wont_process_rollback_with_unknown_transaction()
    {
        $data = $this->getRollbackData();

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn([]);

        $transactionService
            ->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['originalProviderTransactionId'])
            ->once()
            ->andReturn([]);

        $this->app->instance(TransactionService::class, $transactionService);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/rollback', $data);

        $this->assertResponseStatus(404);
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_process_rollback()
    {
        $this->fakeJobs();
        $data = $this->getRollbackData();

        $amount = 100.10;
        $transactionData = array_merge($data, ['amount' => $amount]);

        $transactionWalletData = [
            'id' => 12223131,
            'amount' => $amount,
            'previousBalance' => (float) $amount + 500,
            'currentBalance' => (float) 500,
            'currency' => 'USD',
            'type' => 'bet refund',
            'account' => $data['account'],
        ];
        $builtData = [
            'account' => $data['account'],
            'providerId' => $data['providerId'],
            'originalProviderTransactionId' => '10',
            'providerTransactionId' => $data['providerTransactionId'],
            'amount' => (float) $amount * -1,
            'type' => 'bet refund',
        ];

        $transactionService = Mockery::mock(TransactionService::class);
        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['providerTransactionId'])
            ->once()
            ->andReturn([]);

        $transactionService->shouldReceive('getTransactionData')
            ->with($data['providerId'], $data['originalProviderTransactionId'])
            ->once()
            ->andReturn($transactionData);

        $transactionWallet = factory(TransactionWallet::class)->make($transactionWalletData);
        $transactionRepository = Mockery::mock(TransactionRepository::class);
        $transactionRepository->shouldReceive('create')
            ->with($builtData)
            ->andReturn($transactionWallet);

        $this->app->instance(TransactionRepository::class, $transactionRepository);
        $this->app->instance(TransactionService::class, $transactionService);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->post('/api/v1/rollback', $data);

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
