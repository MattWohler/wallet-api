<?php declare(strict_types=1);

namespace App\Exceptions\Contracts;

interface HandledException
{
    public function getErrors(): array;

    public function getStatus(): int;
}
