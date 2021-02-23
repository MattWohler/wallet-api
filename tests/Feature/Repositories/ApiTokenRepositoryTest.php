<?php declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\ApiToken;
use App\Models\Repositories\Contracts\ApiTokenRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class ApiTokenRepositoryTest extends TestCase
{
    /** @var ApiTokenRepository */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ApiTokenRepository::class);
    }

    public function test_can_find_by_token()
    {
        $generated = factory(ApiToken::class)->create();
        $found = $this->repository->findByToken($generated->token);

        $this->assertEquals($found->id, $generated->id);
    }

    public function test_unknown_token_returns_null()
    {
        $searched = $this->repository->findByToken('token');
        $this->assertNull($searched);
    }

    public function test_can_find_by_name_and_target()
    {
        $generated = factory(ApiToken::class)->create(['name' => 'test', 'target' => 'transaction-api']);
        $found = $this->repository->findByNameAndTarget($generated->name, $generated->target);

        $this->assertEquals($generated->id, $found->id);
    }

    public function test_unknown_name_and_target_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findByNameAndTarget('unknown_name', 'unknown_target');
    }

    public function test_can_deactivate_token()
    {
        $generated = factory(ApiToken::class)->create();
        $this->assertTrue($generated->is_active);

        $this->repository->deactivate($generated);
        $found = $this->repository->findByToken($generated->token);

        $this->assertFalse($found->is_active);
    }

    public function test_can_create_token()
    {
        $created = $this->repository->create('test', 'transaction-api', ['authenticate-account']);

        $this->assertNotNull($created);
        $this->assertNotNull($created->id);
    }

    public function test_cannot_create_token_without_scopes()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing scopes');

        $this->repository->create('test', 'transaction-api', []);
    }

    public function test_cannot_create_token_with_invalid_scopes()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Route [unknown-route] not defined.');

        $this->repository->create('test', 'transaction-api', ['unknown-route']);
    }

    public function test_can_validate_token()
    {
        $this->expectNotToPerformAssertions();

        $generated = factory(ApiToken::class)->create();
        $this->repository->validateToken($generated->token, $generated->scopes[0]);
    }

    public function test_cannot_validate_unknown_token()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Token not found');

        $this->repository->validateToken('unknown', 'route');
    }

    public function test_cannot_validate_inactive_token()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Deactivated token');

        $generated = factory(ApiToken::class)->create(['is_active' => false]);
        $this->assertFalse($generated->is_active);

        $this->repository->validateToken($generated->token, 'route');
    }

    public function test_cannot_validate_unauthorized_route_token()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unauthorized route');

        $generated = factory(ApiToken::class)->create(['scopes' => ['authenticate-account']]);
        $this->repository->validateToken($generated->token, 'internal-get-balance');
    }
}
