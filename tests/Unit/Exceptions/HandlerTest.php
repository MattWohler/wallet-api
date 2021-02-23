<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handled\AuthException;
use App\Exceptions\Handled\DuplicateTransactionException;
use App\Exceptions\Handled\InsufficientFundsException;
use App\Exceptions\Handled\ValidationException;
use App\Exceptions\Handled\WalletServiceException;
use App\Exceptions\Handler;
use App\Services\ApmNotifier;
use App\Services\CerberusNotifier;
use App\Services\NewRelicNotifier;
use App\Support\Logger;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Throwable;

class HandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('expectsJson')->andReturn(true);

        $this->app->instance(Request::Class, $request);
    }

    public function exceptionDataProvider()
    {
        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive('errors')->andReturn(['field' => 'error']);
        $message = 'something went wrong';

        return [
            [new DuplicateTransactionException(123), 409],
            [new InsufficientFundsException(), 403],
            [new WalletServiceException($message), 500],
            [new ValidationException($validator), 422],
            [new AuthException($message), 500],
            [new Exception($message), 500],
        ];
    }

    /**
     * @param  Throwable  $exception
     * @param  int  $expectedStatus
     * @dataProvider exceptionDataProvider
     */
    public function test_handler_handles_exceptions(Throwable $exception, int $expectedStatus)
    {
        $handler = $this->app->make(Handler::class);
        $request = $this->app->make(Request::class);
        $response = $handler->render($request, $exception);

        $this->assertEquals($expectedStatus, $response->status());
        $json = json_decode($response->getContent());

        $this->assertNotNull($json);
        $this->assertAttributeEquals($expectedStatus, 'status', $json);
        $this->assertObjectHasAttribute('errors', $json);
    }

    public function test_exceptions_are_reported_on_failure()
    {
        config(['logging.report_errors' => true]);
        $exception = new Exception('Boom!');

        $cerberus = Mockery::mock(CerberusNotifier::class);
        $cerberus->shouldReceive('captureException')
            ->with($exception)
            ->once()
            ->andReturn();

        $newRelic = Mockery::mock(NewRelicNotifier::class);
        $newRelic->shouldReceive('captureException')
            ->with($exception)
            ->once()
            ->andReturn();

        $sentry = Mockery::mock(app('sentry'));
        $sentry->shouldReceive('captureException')
            ->with($exception)
            ->once()
            ->andReturn();

        $apm = Mockery::mock(ApmNotifier::class);
        $apm->shouldReceive('captureException')
            ->with($exception)
            ->once()
            ->andReturn();

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('error')
            ->with($exception)
            ->once()
            ->andReturn();

        $this->app->instance('sentry', $sentry);
        $this->app->instance(Logger::class, $logger);
        $this->app->instance(ApmNotifier::class, $apm);
        $this->app->instance(CerberusNotifier::class, $cerberus);
        $this->app->instance(NewRelicNotifier::class, $newRelic);


        $handler = $this->app->make(Handler::class);
        $handler->report($exception);
    }
}
