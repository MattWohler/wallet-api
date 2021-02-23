<?php

namespace Tests\Unit\Services\Wallet;

use App\Services\Driver\Dgs\DgsResponseParser;
use App\Services\Driver\Dgs\DgsService;
use App\Support\Logger;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface as Database;
use Mockery;
use Tests\TestCase;

class DgsServiceTest extends TestCase
{
    public function test_can_get_balance()
    {
        $account = $this->faker->uuid;
        $response = [$this->faker->word];

        $database = Mockery::mock(Database::class);
        $database->shouldReceive('select')
            ->with('EXEC prime.GetPlayerBalance ?', [$account])
            ->once()
            ->andReturn($response);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('query')
            ->with('EXEC prime.GetPlayerBalance ?', [$account], $response)
            ->once()
            ->andReturnNull();

        $parser = Mockery::mock(DgsResponseParser::class);
        $parser->shouldReceive('parseBalance')
            ->with($response, ['account' => $account])
            ->once()
            ->andReturn([]);

        $wallet = new DgsService($database, $parser, $logger);
        $response = $wallet->getBalance($account);

        $this->assertEquals([], $response);
    }

    public function test_can_authenticate()
    {
        $account = $this->faker->uuid;
        $response = [$this->faker->word];

        $database = Mockery::mock(Database::class);
        $database->shouldReceive('select')
            ->with('EXEC prime.GetPlayerRow ?', [$account])
            ->once()
            ->andReturn($response);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('query')
            ->with('EXEC prime.GetPlayerRow ?', [$account], $response)
            ->once()
            ->andReturnNull();

        $parser = Mockery::mock(DgsResponseParser::class);
        $parser->shouldReceive('parseAuthentication')
            ->with($response, ['account' => $account])
            ->once()
            ->andReturn([]);

        $wallet = new DgsService($database, $parser, $logger);
        $response = $wallet->authenticate($account);

        $this->assertEquals([], $response);
    }

    public function test_can_get_players_by_accounts()
    {
        $accounts = [$this->faker->uuid, $this->faker->uuid];
        $response = [$this->faker->word];

        $database = Mockery::mock(Database::class);
        $database->shouldReceive('select')
            ->with('EXEC prime.GetPlayerRow_String ?', [implode(',', $accounts)])
            ->once()
            ->andReturn($response);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('query')
            ->with('EXEC prime.GetPlayerRow_String ?', [implode(',', $accounts)], $response)
            ->once()
            ->andReturnNull();

        $parser = Mockery::mock(DgsResponseParser::class);
        $parser->shouldReceive('parsePlayersByAccounts')
            ->with($response)
            ->once()
            ->andReturn([]);

        $wallet = new DgsService($database, $parser, $logger);
        $response = $wallet->getPlayersByAccounts($accounts);

        $this->assertEquals([], $response);
    }

    public function test_can_get_figure_by_range()
    {
        $response = [$this->faker->word];
        $data = [
            'account' => $this->faker->uuid,
            'startDate' => $this->faker->date('Y-m-d H:i:s'),
            'endDate' => $this->faker->date('Y-m-d H:i:s'),
        ];

        $startDate = Carbon::parse($data['startDate']);
        $endDate = Carbon::parse($data['endDate']);

        $database = Mockery::mock(Database::class);
        $database->shouldReceive('select')
            ->with('EXEC prime.GetFigureCasino ?, ?, ?', array_values($data))
            ->once()
            ->andReturn($response);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('query')
            ->with('EXEC prime.GetFigureCasino ?, ?, ?', array_values($data), $response)
            ->once()
            ->andReturnNull();

        $parser = Mockery::mock(DgsResponseParser::class);
        $parser->shouldReceive('parseFigure')
            ->with($response, [
                'account' => $data['account'],
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])
            ->once()
            ->andReturn([]);

        $wallet = new DgsService($database, $parser, $logger);
        $response = $wallet->getFigureByRange($data['account'], $startDate, $endDate);

        $this->assertEquals([], $response);
    }

    public function test_can_get_insert_transaction()
    {
        $response = [$this->faker->word];
        $data = [
            'account' => $this->faker->uuid,
            'description' => $this->faker->word,
            'amount' => $this->faker->randomFloat(),
            'reference' => $this->faker->word,
        ];
        $params = array_merge($data, [
            'fee' => 0,
            'bonus' => 0,
            'paymentMethod' => 'casino w/l',
            'transactionType' => 'K',
            'userId' => 1,
            'adjustmentType' => 'Default',
        ]);

        $database = Mockery::mock(Database::class);
        $database->shouldReceive('select')
            ->with('EXEC prime.InsertPlayerTransactionWS ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', array_values($params))
            ->once()
            ->andReturn($response);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('query')
            ->with('EXEC prime.InsertPlayerTransactionWS ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', array_values($params),
                $response)
            ->once()
            ->andReturnNull();

        $data['type'] = $this->faker->word;
        $params['type'] = $data['type'];

        $parser = Mockery::mock(DgsResponseParser::class);
        $parser->shouldReceive('parseInsertion')
            ->with($response, $params)
            ->once()
            ->andReturn([]);

        $wallet = new DgsService($database, $parser, $logger);
        $response = $wallet->insertTransaction($data);

        $this->assertEquals([], $response);
    }
}
