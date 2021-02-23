<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlayerRequest;
use App\Http\Transformers\PlayerTransformer;
use App\Models\Repositories\Contracts\Wallet\PlayerRepository;

class AuthController extends Controller
{
    public function authenticate(PlayerRepository $repository, PlayerTransformer $transformer, string $account)
    {
        $player = $repository->getPlayer($account);
        return response()->json(['status' => 200, 'response' => $transformer->transform($player)]);
    }

    public function getPlayer(PlayerRepository $repository, PlayerTransformer $transformer, string $account)
    {
        $player = $repository->getPlayer($account, false);
        return response()->json(['status' => 200, 'response' => $transformer->transform($player)]);
    }

    public function getPlayers(PlayerRequest $request, PlayerRepository $repository, PlayerTransformer $transformer)
    {
        $accounts = (array) $request->query('accounts');
        $players = $repository->getPlayers($accounts);

        return response()->json(['status' => 200, 'response' => $transformer->transform($players)]);
    }
}
