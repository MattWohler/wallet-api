<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\Handled\DuplicateTransactionException;
use App\Exceptions\Handled\InsufficientFundsException;
use App\Services\Contracts\TransactionService;
use App\Services\Wallet\Contracts\ServiceInterface as Wallet;
use Illuminate\Validation\Rules\In;

class TransactionRequest extends AbstractRequest
{
    /** @var Wallet */
    protected $wallet;

    /** @var TransactionService */
    protected $service;

    public function injectDependencies(Wallet $wallet, TransactionService $service): void
    {
        $this->wallet = $wallet;
        $this->service = $service;
    }

    public function rules(): array
    {
        return [
            'account' => 'required|string',
            'providerId' => 'required|integer',
            'providerTransactionId' => 'required|string',
            'amount' => 'required|numeric',
            'brandId' => 'required|integer',
            'providerGameId' => 'required|string',
            'roundId' => 'required|string',
            'reference' => 'required|string',
            'currency' => 'required|string',
            'description' => 'string',
            'type' => ['required', 'string', new In(['bet', 'bet cancel', 'bet result', 'bet refund'])]
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

        if ((string) $data['type'] === 'bet') {
            $this->checkBalance($data);
        }
    }

    protected function checkBalance(array $data): void
    {
        $balance = $this->wallet->getBalance($data['account']);
        $amount = $data['amount'];

        if ($amount < 0 && $balance['amount'] < $amount * -1) {
            throw new InsufficientFundsException();
        }
    }
}
