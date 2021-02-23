<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\Handled\UnknownRollbackTransactionException;
use App\Http\Requests\RollbackRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Transformers\TransactionTransformer;
use App\Jobs\TransactionJob;
use App\Models\Repositories\Contracts\Wallet\TransactionRepository as WalletTransactionRepository;
use App\Services\Contracts\TransactionService;

class TransactionController extends Controller
{
    public function process(
        TransactionRequest $request,
        WalletTransactionRepository $repository,
        TransactionTransformer $transformer
    ) {
        $data = $request->all();
        $transaction = $repository->create($data);

        $data = $transaction->getTransactionJobData($data);
        dispatch(new TransactionJob($data));

        return response()->json(['status' => 200, 'response' => $transformer->transform($transaction)]);
    }

    public function rollback(
        RollbackRequest $request,
        WalletTransactionRepository $repository,
        TransactionTransformer $transformer,
        TransactionService $service
    ) {
        $data = $this->getOriginalTransactionData($service, $request->all());
        $transaction = $repository->create($data);

        $data = $transaction->getTransactionJobData($data);
        dispatch(new TransactionJob($data));

        return response()->json(['status' => 200, 'response' => $transformer->transform($transaction)]);
    }

    protected function getOriginalTransactionData(TransactionService $service, array $data): array
    {
        $transaction = $service->getTransactionData((int) $data['providerId'], $data['originalProviderTransactionId']);

        if (empty($transaction)) {
            throw new UnknownRollbackTransactionException($data['originalProviderTransactionId']);
        }

        $transaction['amount'] *= -1;
        $transaction['type'] = 'bet refund';
        $transaction['providerTransactionId'] = $data['providerTransactionId'];

        return $transaction;
    }
}
