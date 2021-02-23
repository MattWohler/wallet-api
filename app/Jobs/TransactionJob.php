<?php declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\Handled\WorkerException;
use App\Models\Repositories\Contracts\TransactionRepository;
use Exception;

class TransactionJob extends Job
{
    /** @var string */
    private $message;

    public function __construct(array $data)
    {
        $this->message = (string) json_encode($data);
    }

    public function handle(TransactionRepository $repository): void
    {
        try {
            $repository->create(json_decode($this->message, true));
        } catch (Exception $e) {
            throw new WorkerException($e->getMessage()."\n".$this->message, (int) $e->getCode(), $e);
        }
    }
}
