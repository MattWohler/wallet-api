<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Auth\GenericUser;
use Tests\TestCase;

class HistoriesTransactionTest extends TestCase
{
    public function test_can_get_transaction_without_filters()
    {
        factory(Transaction::class)->create([
            'id' => 1136077190,
            'wallet_transaction_id' => 123456,
            'old_balance' => 124552.884,
            'new_balance' => 17260209.2021,
            'account' => 'MB4017',
            'provider_transaction_id' => '1531531',
            'round_id' => '21531',
            'amount' => 1183919690,
            'type' => 'bet',
            'provider_id' => 1,
            'provider_game_id' => '11fd31',
            'payload' => '{"payload":"test"}',
            'created_at' => Carbon::create(2019, 02, 01, 00, 00, 00),
        ]);

        factory(Transaction::class)->create([
            'id' => 1531,
            'wallet_transaction_id' => 1236,
            'old_balance' => 1245.884,
            'new_balance' => 172209.2021,
            'account' => 'MB4017',
            'provider_transaction_id' => '153131',
            'round_id' => '2151',
            'amount' => 11839190,
            'type' => 'bet',
            'provider_id' => 1,
            'provider_game_id' => '11fd31',
            'payload' => '{"payload":"test"}',
            'created_at' => Carbon::create(2019, 02, 01, 00, 00, 00)->addDays(8),
        ]);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/histories/transaction', []);

        $this->assertResponseStatus(200);
        $this->assertResponseMatchesJsonSnapshot();
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_get_transaction_with_filters()
    {
        factory(Transaction::class)->create([
            'id' => 1136077190,
            'wallet_transaction_id' => 123456,
            'old_balance' => 124552.884,
            'new_balance' => 17260209.2021,
            'account' => 'MB4017',
            'provider_transaction_id' => '1531531',
            'round_id' => '21531',
            'amount' => 1183919690,
            'type' => 'bet',
            'provider_id' => 1,
            'provider_game_id' => '11fd31',
            'payload' => '{"payload":"test"}',
            'created_at' => Carbon::create(2019, 02, 01, 00, 00, 00),
        ]);

        factory(Transaction::class)->create([
            'wallet_transaction_id' => 123457,
            'account' => 'MB4017',
            'provider_transaction_id' => '1531531',
            'round_id' => '21531',
            'type' => 'bet',
            'provider_id' => 2,
            'created_at' => Carbon::create(2019, 02, 01, 00, 00, 00)->addDays(8),
        ]);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/histories/transaction', [
            'account' => 'MB4017',
            'type' => 'bet',
            'providerId' => 1,
            'walletTransactionId' => 123456,
            'providerTransactionId' => '1531531',
            'roundId' => '21531',
            'startDate' => Carbon::create(2019, 02, 01, 00, 00, 00)->subDays(10),
            'endDate' => Carbon::create(2019, 02, 01, 00, 00, 00)->addDays(5),
        ]);

        $this->assertResponseStatus(200);
        $this->assertResponseMatchesJsonSnapshot();
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_not_get_transaction_when_end_date_is_before_start_date()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/histories/transaction', [
            'account' => 'MB4017',
            'type' => 'bet',
            'providerId' => 1,
            'walletTransactionId' => 123456,
            'providerTransactionId' => '1531531',
            'roundId' => '21531',
            'startDate' => Carbon::now()->addDays(10),
            'endDate' => Carbon::now()->addDays(5),
        ]);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesJsonSnapshot();
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_not_get_transaction__without_right_parameter_types()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/histories/transaction', [
            'account' => true,
            'type' => true,
            'providerId' => '1',
            'walletTransactionId' => '123456',
            'providerTransactionId' => true,
            'roundId' => true,
            'startDate' => true,
            'endDate' => true,
        ]);

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesJsonSnapshot();
        $this->assertResponseMatchesSwagger();
    }

    public function test_can_not_get_transaction__without_authorization()
    {
        $this->get('/api/v1/histories/transaction', []);

        $this->assertResponseStatus(401);
    }
}
