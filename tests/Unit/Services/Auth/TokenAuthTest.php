<?php declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Exceptions\Handled\AuthException;
use App\Models\ApiToken;
use App\Models\Repositories\ApiTokenRepository;
use App\Models\Repositories\Contracts\ApiTokenRepository as ApiTokenRepositoryContract;
use App\Services\Auth\TokenAuth;
use Illuminate\Auth\GenericUser;
use Mockery;
use Tests\TestCase;

class TokenAuthTest extends TestCase
{
    public function test_can_authenticate()
    {
        $authorized = factory(GenericUser::class)->make(['token' => 'valid']);

        $repository = Mockery::mock(ApiTokenRepositoryContract::class);
        $repository->shouldReceive('validateToken')
            ->with($authorized->token, '')
            ->once()
            ->andReturn();

        $this->app->instance(ApiTokenRepositoryContract::class, $repository);
        $auth = $this->app->make(TokenAuth::class);

        $authenticated = $auth->authenticate($authorized->token);
        $this->assertNotNull($authenticated);

        $this->assertInstanceOf(GenericUser::class, $authenticated);
        $this->assertEquals($authenticated->token, $authorized->token);
    }

    public function test_cannot_authenticate_unknown_token()
    {
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Token not found');

        $auth = $this->app->make(TokenAuth::class);
        $auth->authenticate('unknown');
    }

    public function test_cannot_authenticate_inactive_token()
    {
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Deactivated token');

        $token = factory(ApiToken::class)->make(['token' => 'deactivated', 'is_active' => false]);

        $repository = Mockery::mock(ApiTokenRepository::class)->makePartial();
        $repository->shouldReceive('findByToken')
            ->with($token->token)
            ->once()
            ->andReturn($token);


        $this->app->instance(ApiTokenRepositoryContract::class, $repository);
        $auth = $this->app->make(TokenAuth::class);

        $auth->authenticate($token->token);
    }

    public function test_cannot_authenticate_unauthorized_route_token()
    {
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Unauthorized route');

        $token = factory(ApiToken::class)->make(['token' => 'unauthorized', 'scopes' => ['internal-authenticate']]);

        $repository = Mockery::mock(ApiTokenRepository::class)->makePartial();
        $repository->shouldReceive('findByToken')
            ->with($token->token)
            ->once()
            ->andReturn($token);


        $this->app->instance(ApiTokenRepositoryContract::class, $repository);
        $auth = $this->app->make(TokenAuth::class);

        $auth->authenticate($token->token, 'unauthorized');
    }
}
