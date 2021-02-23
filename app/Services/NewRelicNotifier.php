<?php declare(strict_types=1);

namespace App\Services;

use Throwable;

class NewRelicNotifier
{
    public function captureException(Throwable $exception, string $level = 'error'): void
    {
        if (extension_loaded('newrelic')) {
            newrelic_notice_error('level:'.$level, $exception);
        }
    }
}
