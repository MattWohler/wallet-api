<?php declare(strict_types=1);

namespace App\Services;

use PhilKra\Agent;
use Throwable;

class ApmNotifier
{
    /** @var Agent */
    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function captureException(Throwable $exception): void
    {
        if ((bool) config('elastic-apm.active')) {
            $this->agent->captureThrowable($exception);
            $this->agent->send();
        }
    }
}
