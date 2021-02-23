<?php declare(strict_types=1);

namespace App\Services\Driver\Dgs;

use App\Services\Wallet\Contracts\ServiceInterface;
use App\Support\Logger;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface as Database;

class DgsService implements ServiceInterface
{
    /** @var Database */
    protected $database;

    /** @var DgsResponseParser */
    protected $parser;

    /** @var Logger */
    protected $logger;

    public function __construct(Database $database, DgsResponseParser $parser, Logger $logger)
    {
        $this->database = $database;
        $this->parser = $parser;
        $this->logger = $logger;
    }

    protected function select(string $sql, array $bindings): array
    {
        $response = $this->database->select($sql, $bindings);
        $this->logger->query($sql, $bindings, $response);

        return $response;
    }

    public function authenticate(string $account): array
    {
        $response = $this->select('EXEC prime.GetPlayerRow ?', [$account]);
        return $this->parser->parseAuthentication($response, ['account' => $account]);
    }

    public function getBalance(string $account): array
    {
        $response = $this->select('EXEC prime.GetPlayerBalance ?', [$account]);
        return $this->parser->parseBalance($response, ['account' => $account]);
    }

    public function getPlayersByAccounts(array $accounts): array
    {
        $response = $this->select('EXEC prime.GetPlayerRow_String ?', [implode(',', $accounts)]);
        return $this->parser->parsePlayersByAccounts($response);
    }

    public function getFigureByRange(
        string $account,
        Carbon $startDate,
        Carbon $endDate,
        string $format = 'Y-m-d H:i:s'
    ): array {
        $response = $this->select('EXEC prime.GetFigureCasino ?, ?, ?', [
            $account,
            $startDate->format($format),
            $endDate->format($format),
        ]);

        return $this->parser->parseFigure($response, [
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function insertTransaction(array $data): array
    {
        $params = $this->buildInsertionParams($data);
        $response =
            $this->select('EXEC prime.InsertPlayerTransactionWS ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', array_values($params));

        $params['type'] = $data['type'];
        return $this->parser->parseInsertion($response, $params);
    }

    private function buildInsertionParams(array $data): array
    {
        // Warning - the order of the fields is important for insertion
        return [
            'account' => array_get($data, 'account'),
            'description' => array_get($data, 'description'),
            'amount' => array_get($data, 'amount'),
            'reference' => array_get($data, 'reference'),
            'fee' => array_get($data, 'fee', 0),
            'bonus' => array_get($data, 'bonus', 0),
            'paymentMethod' => array_get($data, 'paymentMethod', 'casino w/l'),
            'transactionType' => array_get($data, 'transactionType', 'K'),
            'userId' => array_get($data, 'userId', 1),
            'adjustmentType' => array_get($data, 'adjustmentType', 'Default'),
        ];
    }
}
