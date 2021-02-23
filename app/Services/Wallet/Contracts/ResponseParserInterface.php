<?php declare(strict_types=1);

namespace App\Services\Wallet\Contracts;

use App\Exceptions\Handled\WalletServiceException;

interface ResponseParserInterface
{
    /**
     * Parse the authenticate response of the wallet service
     *
     * @param  array  $response
     * @param  array  $data
     * @return array
     * @throws WalletServiceException
     */
    public function parseAuthentication(array $response, array $data): array;

    /**
     * Parse the get players by accounts response of the wallet service
     *
     * @param  array  $responses
     * @return array
     * @throws WalletServiceException
     */
    public function parsePlayersByAccounts(array $responses): array;

    /**
     * Parse the balance response of the wallet service
     *
     * @param  array  $response
     * @param  array  $data
     * @return array
     * @throws WalletServiceException
     */
    public function parseBalance(array $response, array $data): array;

    /**
     * Parse the figure response of the wallet service
     *
     * @param  array  $response
     * @param  array  $data
     * @return array
     * @throws WalletServiceException
     */
    public function parseFigure(array $response, array $data): array;

    /**
     * Parse the insertion response of the wallet service
     *
     * @param  array  $response
     * @param  array  $data
     * @return array
     * @throws WalletServiceException
     */
    public function parseInsertion(array $response, array $data): array;
}
