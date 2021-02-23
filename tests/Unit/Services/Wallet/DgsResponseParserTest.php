<?php

namespace Tests\Unit\Services\Wallet;

use App\Exceptions\Handled\WalletServiceException;
use App\Services\Driver\Dgs\DgsResponseParser;
use stdClass;
use Tests\TestCase;

class DgsResponseParserTest extends TestCase
{
    /** @var DgsResponseParser */
    protected $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(DgsResponseParser::class);
    }

    public function test_empty_response_throws_exception()
    {
        $this->expectException(WalletServiceException::class);
        $this->expectExceptionMessage('Empty response from DGS');

        $this->parser->parseFigure([], []);
    }

    public function test_dgs_errors_throws_exception()
    {
        $this->expectException(WalletServiceException::class);
        $this->expectExceptionMessage('BOOM!!!');

        $response = new stdClass();
        $response->errorCode = 500;
        $response->ErrorMsg = 'BOOM!!!';

        $this->parser->parseFigure([$response], []);
    }

    public function test_default_currency_is_usd()
    {
        $response = new stdClass();
        $response->currentbalance = 40;

        $data = ['account' => 'MB1001'];
        $balanceData = $this->parser->parseBalance([$response], $data);

        $this->assertArrayHasKey('currency', $balanceData);
        $this->assertEquals('USD', $balanceData['currency']);
    }

    public function test_can_parse_balance()
    {
        $response = new stdClass();
        $response->currentbalance = (string) $this->faker->randomFloat();
        $response->Currency = $this->faker->currencyCode;

        $data = ['account' => 132];
        $balanceData = $this->parser->parseBalance([$response], $data);

        $this->assertEquals([
            'account' => $data['account'],
            'amount' => (float) $response->currentbalance,
            'currency' => $response->Currency,
        ], $balanceData);
    }

    public function test_can_parse_authentication()
    {
        $response = new stdClass();
        $response->Currency = $this->faker->currencyCode;
        $account = $this->faker->randomDigit;

        $data = $this->parser->parseAuthentication([$response], ['account' => $account]);

        $this->assertEquals([
            'id' => 0,
            'account' => $account,
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'brand' => '',
            'brandId' => 0,
            'balance' => null,
            'currency' => $response->Currency,
            'country' => '',
            'enableCasino' => true,
            'enableCards' => true,
            'enableHorses' => true,
            'enableSports' => true,
            'isTestAccount' => false
        ], $data);
    }

    public function test_can_parse_figure()
    {
        $response = new stdClass();
        $response->LoseAmount = (string) $this->faker->randomFloat();
        $response->WinAmount = (string) $this->faker->randomFloat();
        $response->Currency = $this->faker->currencyCode;

        $data = [
            'account' => 132,
            'startDate' => '2018-01-01',
            'endDate' => '2018-01-01',
        ];

        $figureData = $this->parser->parseFigure([$response], $data);

        $this->assertEquals([
            'account' => $data['account'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'loseAmount' => (float) $response->LoseAmount,
            'winAmount' => (float) $response->WinAmount,
            'currency' => $response->Currency,
        ], $figureData);
    }

    public function test_can_parse_insertion()
    {
        $response = new stdClass();
        $response->IdTrasaction = (string) $this->faker->randomDigit; // typo in stored procedure
        $response->PreviousBalance = (string) $this->faker->randomFloat();
        $response->CurrentBalance = (string) $this->faker->randomFloat();
        $response->Currency = $this->faker->currencyCode;

        $data = [
            'account' => 132,
            'amount' => '132121.32',
            'type' => 'bet',
        ];

        $insertionData = $this->parser->parseInsertion([$response], $data);

        $this->assertEquals([
            'id' => $response->IdTrasaction,
            'type' => $data['type'],
            'account' => $data['account'],
            'amount' => (float) $data['amount'],
            'previousBalance' => (float) $response->PreviousBalance,
            'currentBalance' => (float) $response->CurrentBalance,
            'currency' => $response->Currency,
        ], $insertionData);
    }

    public function test_can_parse_players_by_accounts()
    {
        $account = $this->faker->randomDigit;

        $response = new stdClass();
        $response->Currency = $this->faker->currencyCode;
        $response->Player = $account;

        $data = $this->parser->parsePlayersByAccounts([$response, clone $response]);
        $expected = [
            'id' => 0,
            'account' => $account,
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'brand' => '',
            'brandId' => 0,
            'balance' => null,
            'currency' => $response->Currency,
            'country' => '',
            'enableCasino' => true,
            'enableCards' => true,
            'enableHorses' => true,
            'enableSports' => true,
            'isTestAccount' => false
        ];

        $this->assertEquals([$expected, $expected], $data);
    }
}
