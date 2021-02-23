<?php

namespace Tests\Feature;

use App\Models\Repositories\Contracts\Wallet\PlayerRepository;
use Illuminate\Auth\GenericUser;
use Tests\Fakes\Repositories\FakePlayerRepository;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_cannot_authenticate_without_authorization()
    {
        $this->get('/api/v1/authenticate/1345');

        $this->assertResponseStatus(401);
    }

    public function test_authenticate()
    {
        $account = '123456';
        $this->app->alias(FakePlayerRepository::class, PlayerRepository::class);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/authenticate/'.$account);

        $this->assertResponseOk();
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('player', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
    }

    public function test_cannot_get_player_without_authorization()
    {
        $this->get('/api/v1/player/1345');

        $this->assertResponseStatus(401);
    }

    public function test_get_player()
    {
        $account = '123456';
        $this->app->alias(FakePlayerRepository::class, PlayerRepository::class);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/player/'.$account);

        $this->assertResponseOk();
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('player', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
    }

    public function test_cannot_get_player_by_batch_without_authorization()
    {
        $this->get('/api/v1/player');

        $this->assertResponseStatus(401);
    }

    public function test_can_get_players_by_batch()
    {
        $accounts = ['MB1001', 'MB4017'];
        $this->app->alias(FakePlayerRepository::class, PlayerRepository::class);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/player', compact('accounts'));

        $this->assertResponseOk();
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('player', 'type', $response->data[0]);

        $this->assertResponseMatchesJsonSnapshot();
    }

    public function test_cannot_get_players_by_batch_without_valid_parameters()
    {
        $authorized = factory(GenericUser::class)->make();

        $this->app->alias(FakePlayerRepository::class, PlayerRepository::class);

        $this->actingAs($authorized)->get('/api/v1/player');
        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();

        $this->actingAs($authorized)->get('/api/v1/player', ['accounts' => 'mb1001']);
        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();

        $this->actingAs($authorized)->get('/api/v1/player', ['accounts' => [45641]]);
        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }
}
