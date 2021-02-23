<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\Handled\DuplicateTransactionException;
use App\Services\Contracts\TransactionService;

class RollbackRequest extends AbstractRequest
{
    /** @var TransactionService */
    protected $service;

    public function injectDependencies(TransactionService $service): void
    {
        $this->service = $service;
    }

    public function rules(): array
    {
        return [
            'account' => 'required|string',
            'providerId' => 'required|integer',
            'originalProviderTransactionId' => 'required|string',
            'providerTransactionId' => 'required|string',
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();
        $data = $this->all();

        $providerTransactionId = (string) $data['providerTransactionId'];
        $transaction = $this->service->getTransactionData((int) $data['providerId'], $providerTransactionId);

        if (!empty($transaction)) {
            throw new DuplicateTransactionException($providerTransactionId);
        }
    }
}
