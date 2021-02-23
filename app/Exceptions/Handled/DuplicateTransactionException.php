<?php declare(strict_types=1);

namespace App\Exceptions\Handled;

use App\Exceptions\Contracts\HandledException;
use Exception;
use Throwable;

class DuplicateTransactionException extends Exception implements HandledException
{
    /**  @var string */
    private $transactionId;

    public function __construct(
        string $transactionId,
        string $message = 'Duplicate Transaction',
        int $code = 409,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->transactionId = $transactionId;
    }

    public function getErrors(): array
    {
        return [
            'transactionId' => $this->transactionId,
            'message' => $this->getMessage(),
        ];
    }

    public function getStatus(): int
    {
        return $this->code;
    }
}
