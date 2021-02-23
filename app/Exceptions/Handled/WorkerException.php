<?php declare(strict_types=1);

namespace App\Exceptions\Handled;

use App\Exceptions\Contracts\HandledException;
use Exception;

class WorkerException extends Exception implements HandledException
{
    public function getErrors(): array
    {
        return [
            $this->getMessage()
        ];
    }

    public function getStatus(): int
    {
        return (int) $this->getCode();
    }
}
