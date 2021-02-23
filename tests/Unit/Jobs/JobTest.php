<?php

namespace Tests\Unit\Jobs;

use Exception;
use Mockery;
use Tests\Fakes\Jobs\FakeFailingJob;
use Tests\TestCase;

class JobTest extends TestCase
{
    public function test_exceptions_are_reported_on_failure()
    {
        putenv('REPORT_ERRORS=true');
        $message = 'Job failed!';

        $job = new FakeFailingJob($message);
        $exception = new Exception($message);

        $sentry = Mockery::mock(app('sentry'));
        $sentry->shouldReceive('captureException')
            ->with($exception)
            ->once();

        $this->app->instance('sentry', $sentry);

        $job->failed($exception);
    }
}
