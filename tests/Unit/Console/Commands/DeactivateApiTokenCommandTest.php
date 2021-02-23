<?php declare(strict_types=1);

namespace Test\Unit\Console\Commands;

use App\Models\ApiToken;
use App\Models\Repositories\Contracts\ApiTokenRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class DeactivateApiTokenCommandTest extends TestCase
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

    public function test_can_not_handle_when_model_not_found_exception()
    {
        $name = 'name';
        $target = 'target';

        $this->apiTokenRepository
            ->shouldReceive('findByNameAndTarget')
            ->once()
            ->with($name, $target)
            ->andThrow(new ModelNotFoundException());

        $this->apiTokenRepository->shouldNotReceive('deactivate');

        $this->artisan('token:deactivate', ['name' => $name, 'target' => $target]);
    }

    public function test_can_not_handle_when_exception()
    {
        $name = 'name';
        $target = 'target';

        $this->apiTokenRepository
            ->shouldReceive('findByNameAndTarget')
            ->once()
            ->with($name, $target)
            ->andThrow(new Exception());

        $this->apiTokenRepository->shouldNotReceive('deactivate');

        $this->artisan('token:deactivate', ['name' => $name, 'target' => $target]);
    }

    public function test_can_handle()
    {
        $name = 'name';
        $target = 'target';
        $token = factory(ApiToken::class)->make();

        $this->apiTokenRepository
            ->shouldReceive('findByNameAndTarget')
            ->once()
            ->with($name, $target)
            ->andReturn($token);

        $this->apiTokenRepository
            ->shouldReceive('deactivate')
            ->once()
            ->with($token);

        $this->artisan('token:deactivate', ['name' => $name, 'target' => $target]);
    }
}
