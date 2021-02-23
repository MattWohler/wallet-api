<?php declare(strict_types=1);

namespace App\Exceptions\Handled;

use App\Exceptions\Contracts\HandledException;
use Exception;
use Throwable;

class WalletServiceException extends Exception implements HandledException
{
    public function __construct(string $message, int $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return [
            'message' => $this->getMessage(),
        ];
    }

    public function getStatus(): int
    {
        return $this->code;
    }
}
