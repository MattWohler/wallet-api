<?php

namespace Tests\Fakes\Jobs;

use App\Jobs\Job;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class FakeFailingJob extends Job implements ShouldQueue
{
    /** @var string */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        throw new Exception($this->message);
    }
}
