<?php

namespace Tests\Feature;

use App\Models\Repositories\Contracts\Wallet\BalanceRepository;
use Illuminate\Auth\GenericUser;
use Tests\Fakes\Repositories\FakeBalanceRepository;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    public function test_cannot_get_balance_without_authorization()
    {
        $this->get('/api/v1/balance/1345');

        $this->assertResponseStatus(401);
    }

    public function test_get_balance()
    {
        $account = '123456';
        $this->app->alias(FakeBalanceRepository::class, BalanceRepository::class);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/balance/'.$account);

        $this->assertResponseOk();
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('balance', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
    }
}
