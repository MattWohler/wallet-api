<?php declare(strict_types=1);

namespace App\Services\Wallet\Contracts;

use App\Exceptions\Handled\WalletServiceException;
use Carbon\Carbon;

interface ServiceInterface
{
    /**
     * Authenticate an account
     *
     * @param  string  $account
     * @return array
     * @throws WalletServiceException
     */
    public function authenticate(string $account): array;

    /**
     * Get the balance of an account
     *
     * @param  string  $account
     * @return array
     * @throws WalletServiceException
     */
    public function getBalance(string $account): array;

    /**
     * Get players' info by accounts
     *
     * @param  string[]  $accounts
     * @return array
     * @throws WalletServiceException
     */
    public function getPlayersByAccounts(array $accounts): array;

    /**
     * Get figure for an account by date range
     *
     * @param  string  $account
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @param  string  $format
     * @return array
     * @throws WalletServiceException
     */
    public function getFigureByRange(
        string $account,
        Carbon $startDate,
        Carbon $endDate,
        string $format = 'Y-m-d H:i:s'
    ): array;

    /**
     * Insert a transaction into the wallet service
     *
     * @param  array  $data
     * @return array
     * @throws WalletServiceException
     */
    public function insertTransaction(array $data): array;
}
