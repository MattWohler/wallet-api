<?php

namespace Tests\Unit\Support;

use App\Support\Logger;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class LoggerTest extends TestCase
{
    /** @var Mockery\MockInterface */
    private $logger;

    /** @var Logger */
    private $log;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->app->instance(LoggerInterface::class, $this->logger);

        $this->log = $this->app->make(Logger::class, [$this->logger]);
    }

    public function test_can_log_level_info()
    {
        $request = new Request(['key' => 'value']);
        $response = new JsonResponse(['response' => 'value']);

        $this->logger->shouldReceive('info')
            ->once()
            ->andReturnUsing(function ($json) use ($request, $response) {
                $data = json_decode($json, true);
                $this->assertArraySubset([
                    'method' => $request->getMethod(),
                    'endpoint' => $request->path(),
                    'request' => $request->all(),
                    'response' => $response->getData(true),
                ], $data);
            });

        $this->log->info($request, $response);
    }

    public function test_can_log_level_error()
    {
        $exception = new Exception('Terrible error');

        $this->logger->shouldReceive('warning')
            ->once()
            ->andReturnUsing(function ($json) use ($exception) {
                $data = json_decode($json, true);
                $this->assertArraySubset([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ], $data);
            });

        $this->logger->shouldNotReceive('channel');

        $this->log->error($exception);
    }

    public function test_can_log_error_level_with_error_channel()
    {
        $manager = Mockery::mock(LogManager::class);
        $this->app->instance(LoggerInterface::class, $manager);

        $manager->shouldReceive('warning')
            ->once()
            ->andReturn();

        $logger = Mockery::mock(LoggerInterface::class);
        $manager->shouldReceive('channel')
            ->with('error')
            ->once()
            ->andReturn($logger);

        $exception = new Exception('Terrible error');
        $logger->shouldReceive('error')
            ->once()
            ->andReturnUsing(function ($json) use ($exception) {
                $data = json_decode($json, true);
                $this->assertArraySubset([
                    'error' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ], $data);
            });

        $log = $this->app->make(Logger::class, [$manager]);
        $log->error($exception);
    }

    public function test_can_log_dgs_interaction()
    {
        $manager = Mockery::mock(LogManager::class);
        $this->app->instance(LoggerInterface::class, $manager);

        $logger = Mockery::mock(LoggerInterface::class);
        $manager->shouldReceive('channel')
            ->with('dgs')
            ->once()
            ->andReturn($logger);

        $sql = 'select something from some table';
        $logger->shouldReceive('info')
            ->once()
            ->andReturnUsing(function ($json) use ($sql) {
                $data = json_decode($json, true);
                $this->assertArraySubset([
                    'sql' => $sql,
                    'bindings' => ['bindings'],
                    'response' => ['response']
                ], $data);
            });

        $log = $this->app->make(Logger::class, [$manager]);
        $log->query($sql, ['bindings'], ['response']);
    }
}
