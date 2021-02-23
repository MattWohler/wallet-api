<?php declare(strict_types=1);

namespace App\Services\Driver\Dgs;

use App\Exceptions\Handled\WalletServiceException;
use App\Services\Wallet\Contracts\ResponseParserInterface;

class DgsResponseParser implements ResponseParserInterface
{
    public function parseAuthentication(array $response, array $data): array
    {
        $response = $this->validate($response);

        $account = array_get($response, 'Player', array_get($data, 'account', ''));
        $agent = array_get($response, 'Agent', '');
        $office = array_get($response, 'OfficeName', '');

        return [
            'id' => (int) array_get($response, 'IdPlayer', 0),
            'account' => (string) $account,
            'title' => array_get($response, 'Title', ''),
            'firstName' => array_get($response, 'Name', ''),
            'lastName' => array_get($response, 'LastName', ''),
            'brand' => array_get($response, 'Book', ''),
            'brandId' => (int) array_get($response, 'IdBook', 0),
            'balance' => null,
            'currency' => array_get($response, 'Currency', 'USD'),
            'country' => array_get($response, 'Country', ''),
            'enableCasino' => (bool) array_get($response, 'EnableCasino', true),
            'enableCards' => (bool) array_get($response, 'EnableCards', true),
            'enableHorses' => (bool) array_get($response, 'EnableHorses', true),
            'enableSports' => (bool) array_get($response, 'EnableSports', true),
            'isTestAccount' => str_contains(mb_strtolower($agent ?? $office), 'test')
        ];
    }

    public function parsePlayersByAccounts(array $responses, array $data = []): array
    {
        $parsed = [];

        foreach ($responses as $response) {
            $parsed[] = $this->parseAuthentication([$response], $data);
        }

        return $parsed;
    }

    public function parseBalance(array $response, array $data): array
    {
        $response = $this->validate($response);

        return [
            'account' => $data['account'],
            'amount' => (float) $response['currentbalance'],
            'currency' => $response['Currency'],
        ];
    }

    public function parseFigure(array $response, array $data): array
    {
        $response = $this->validate($response);

        return [
            'account' => $data['account'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'loseAmount' => (float) $response['LoseAmount'],
            'winAmount' => (float) $response['WinAmount'],
            'currency' => $response['Currency'],
        ];
    }

    public function parseInsertion(array $response, array $data): array
    {
        $response = $this->validate($response);

        return [
            'id' => $response['IdTrasaction'], // typo in stored procedure
            'type' => $data['type'],
            'account' => $data['account'],
            'amount' => (float) $data['amount'],
            'previousBalance' => (float) $response['PreviousBalance'],
            'currentBalance' => (float) $response['CurrentBalance'],
            'currency' => $response['Currency'],
        ];
    }

    /**
     * @param  array  $response
     * @return array
     * @throws WalletServiceException
     */
    private function validate(array $response): array
    {
        $response = get_object_vars($response[0] ?? (object) []);

        if (collect($response)->isEmpty()) {
            throw new WalletServiceException('Empty response from DGS');
        }

        if (isset($response['ErrorCode']) || isset($response['errorCode'])) {
            throw new WalletServiceException(array_get($response, 'ErrorMsg', 'DGS internal error'));
        }

        if (empty($response['Currency'])) {
            $response['Currency'] = 'USD';
        }

        return $response;
    }
}
