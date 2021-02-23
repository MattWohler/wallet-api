<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Repositories\ApiTokenRepository;
use App\Models\Repositories\Contracts\ApiTokenRepository as ApiTokenRepositoryContract;
use App\Models\Repositories\Contracts\TransactionRepository as TransactionRepositoryContract;
use App\Models\Repositories\Contracts\Wallet\BalanceRepository as BalanceRepositoryContract;
use App\Models\Repositories\Contracts\Wallet\FigureRepository as FigureRepositoryContract;
use App\Models\Repositories\Contracts\Wallet\PlayerRepository as PlayerRepositoryContract;
use App\Models\Repositories\Contracts\Wallet\TransactionRepository as WalletTransactionRepositoryContract;
use App\Models\Repositories\TransactionRepository;
use App\Models\Repositories\Wallet\BalanceRepository;
use App\Models\Repositories\Wallet\FigureRepository;
use App\Models\Repositories\Wallet\PlayerRepository;
use App\Models\Repositories\Wallet\TransactionRepository as WalletTransactionRepository;
use App\Services\Contracts\TransactionService as TransactionServiceContract;
use App\Services\TransactionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ApiTokenRepositoryContract::class, ApiTokenRepository::class);

        $this->app->bind(BalanceRepositoryContract::class, BalanceRepository::class);
        $this->app->bind(FigureRepositoryContract::class, FigureRepository::class);

        $this->app->bind(WalletTransactionRepositoryContract::class, WalletTransactionRepository::class);
        $this->app->bind(TransactionRepositoryContract::class, TransactionRepository::class);

        $this->app->bind(PlayerRepositoryContract::class, PlayerRepository::class);
        $this->app->bind(TransactionServiceContract::class, TransactionService::class);
    }
}
