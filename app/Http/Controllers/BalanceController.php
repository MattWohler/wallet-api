<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Transformers\BalanceTransformer;
use App\Models\Repositories\Contracts\Wallet\BalanceRepository;

class BalanceController extends Controller
{
    public function getBalance(BalanceRepository $repository, BalanceTransformer $transformer, string $account)
    {
        $balance = $repository->getBalance($account);
        return response()->json(['status' => 200, 'response' => $transformer->transform($balance)]);
    }
}
