<?php declare(strict_types=1);

namespace Test\Unit\Console\Commands;

use App\Models\ApiToken;
use App\Models\Repositories\Contracts\ApiTokenRepository;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GenerateApiTokenCommandTest extends TestCase
{
    /** @var MockInterface */
    protected $apiTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $apiTokenRepository = Mockery::mock(ApiTokenRepository::class);
        $this->app->instance(ApiTokenRepository::class, $apiTokenRepository);
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function test_can_not_handle_when_exception()
    {
        $name = 'name';
        $target = 'target';
        $scopes = ['route1', 'route2'];

        $this->apiTokenRepository
            ->shouldReceive('create')
            ->once()
            ->with($name, $target, $scopes)
            ->andThrow(new Exception('Integrity constraint violation'));

        $this->artisan('token:generate', ['name' => $name, 'target' => $target, 'scopes' => $scopes]);
    }

    public function test_can_handle()
    {
        $name = 'name';
        $target = 'target';
        $token = factory(ApiToken::class)->make();
        $scopes = ['route1', 'route2'];

        $this->apiTokenRepository
            ->shouldReceive('create')
            ->once()
            ->with($name, $target, $scopes)
            ->andReturn($token);

        $this->artisan('token:generate', ['name' => $name, 'target' => $target, 'scopes' => $scopes]);
    }
}
