<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FigureRequest;
use App\Http\Transformers\FigureTransformer;
use App\Models\Repositories\Contracts\Wallet\FigureRepository;
use Carbon\Carbon;

class FigureController extends Controller
{
    public function getFigure(
        FigureRequest $request,
        FigureRepository $repository,
        FigureTransformer $transformer,
        string $account
    ) {
        $startDate = Carbon::parse($request->get('startDate'));
        $endDate = Carbon::parse($request->get('endDate'));

        $figure = $repository->getFigure($account, $startDate, $endDate);
        return response()->json(['status' => 200, 'response' => $transformer->transform($figure)]);
    }
}
