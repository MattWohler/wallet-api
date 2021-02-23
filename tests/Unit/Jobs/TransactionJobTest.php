<?php

namespace Tests\Unit\Jobs;

use App\Jobs\TransactionJob;
use App\Models\Repositories\Contracts\TransactionRepository;
use Mockery;
use Tests\TestCase;

class TransactionJobTest extends TestCase
{
    public function test_job_can_be_dispatched()
    {
        $this->fakeJobs();

        dispatch(new TransactionJob(['data' => 'data']));
        $this->assertJobsDispatched(TransactionJob::class);
    }

    public function test_transaction_is_created()
    {
        $data = $this->buildData();
        $job = new TransactionJob($data);

        $repository = Mockery::mock(TransactionRepository::class);
        $repository->shouldReceive('create')->once()->with($data)->andReturn();

        $job->handle($repository);
    }

    private function buildData()
    {
        return [
            'walletTransactionId' => 15131,
            'providerId' => 1,
            'providerGameId' => 392,
            'oldBalance' => '1050.00',
            'newBalance' => '1040.00',
            'account' => 'MB4017',
            'providerTransactionId' => '83131313',
            'roundId' => '15313',
            'amount' => '-10',
            'type' => 'bet',
        ];
    }
}
