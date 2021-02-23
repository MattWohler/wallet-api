<?php declare(strict_types=1);

namespace App\Exceptions\Handled;

use App\Exceptions\Contracts\HandledException;
use Illuminate\Validation\ValidationException as Exception;

class ValidationException extends Exception implements HandledException
{
    public function getErrors(): array
    {
        return collect($this->validator->errors())->flatten()->all();
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
