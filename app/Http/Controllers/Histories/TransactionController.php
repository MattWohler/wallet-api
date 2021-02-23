<?php declare(strict_types=1);

namespace App\Http\Controllers\Histories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Histories\TransactionRequest;
use App\Http\Transformers\Histories\TransactionTransformer;
use App\Models\Repositories\Contracts\TransactionRepository;

class TransactionController extends Controller
{
    public function getTransactions(
        TransactionRequest $request,
        TransactionRepository $repository,
        TransactionTransformer $transformer
    ) {
        $transactions = $repository->findAll($request->all());
        return response()->json(['status' => 200, 'response' => $transformer->transform($transactions)]);
    }
}
