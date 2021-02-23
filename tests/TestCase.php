<?php

namespace Tests;

use App\Console\Kernel;
use App\Services\SwaggerYmlLoader;
use Exception;
use Faker\Factory;
use Faker\Generator;
use FR3D\SwaggerAssertions\JsonSchema\RefResolver;
use FR3D\SwaggerAssertions\PhpUnit\SymfonyAssertsTrait;
use FR3D\SwaggerAssertions\SchemaManager;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\MailFake;
use JsonSchema\Uri\Retrievers\PredefinedArray;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use stdClass;

abstract class TestCase extends LumenTestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;
    use MatchesSnapshots;

    use SymfonyAssertsTrait {
        assertResponseMatch as private;
    }

    /** @var string - Current HTTP method */
    protected $currentMethod;

    /** @var JsonResponse */
    protected $response;

    protected static $schemaManager;

    /** @var Generator - The Faker instance */
    protected $faker;

    /** @var string[] */
    protected $dispatchedJobs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->makeFaker();
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function faker($locale = null)
    {
        return is_null($locale) ? $this->faker : $this->makeFaker($locale);
    }

    protected function makeFaker($locale = null)
    {
        return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
    }

    public function assertResponseMatchesSwagger(string $path = null, string $method = null): void
    {
        if ($path === null) {
            $path = str_replace($this->baseUrl, '', $this->currentUri);
            $path = explode('?', $path)[0];
        }
        if ($method === null) {
            $method = $this->currentMethod;
        }

        $this->assertResponseMatch($this->response, $this->getSchemaManager(), $path, $method);
    }

    /**
     * parse swagger.json and return the schema
     * @return SchemaManager
     */
    protected function getSchemaManager(): SchemaManager
    {
        if (!self::$schemaManager) {
            $json = $this->app->make(SwaggerYmlLoader::class)->parse(resource_path('swagger.yml'));

            $refResolver = new RefResolver(
                (new UriRetriever())->setUriRetriever(new PredefinedArray([
                    'http://localhost/v1/swagger.json' => json_encode($json),
                ])),
                new UriResolver()
            );

            /** @var stdClass $refs */
            $refs = $refResolver->resolve('http://localhost/v1/swagger.json');
            self::$schemaManager = new SchemaManager($refs);
        }
        return self::$schemaManager;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->currentMethod = $method;
        $this->response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
        return $this->response;
    }

    public function get($uri, array $parameters = [], array $headers = [])
    {
        $headers = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        return $this->call('GET', $uri, $parameters, [], [], $this->transformHeadersToServerVars($headers));
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $data = json_encode($data);
        $headers = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);

        return $this->call('POST', $uri, [], [], [], $this->transformHeadersToServerVars($headers), $data);
    }

    public function artisan($command, $parameters = [])
    {
        $kernel = new Kernel($this->app);
        return $this->code = $kernel->call($command, $parameters);
    }

    /**
     * @return JsonResponse
     */
    public function assertResponseMatchesJsonSnapshot()
    {
        $this->assertMatchesSnapshot($this->response, new JsonResponseDriver());
        return $this->response;
    }

    /**
     * @param  array  $expected
     * @param $actual
     * @param  string  $message
     * @return JsonResponse
     */
    public function assertJsonSubset(array $expected, $actual, string $message = '')
    {
        if (isset($expected['attributes'])) {
            $expected['attributes'] = array_filter($expected['attributes'], function ($value) {
                return $value !== null;
            });
        } else {
            $expected = array_filter($expected, function ($value) {
                return $value !== null;
            });
        }

        // Associative array only
        if (is_object($actual)) {
            $actual = json_encode($actual);
        }
        if (is_string($actual)) {
            $actual = json_decode($actual, true);
        }

        $this->assertArraySubset($expected, $actual, false, $message);
        return $this->response;
    }

    /**
     * @return JsonResponse
     */
    public function assertResponseOk()
    {
        $actual = $this->response->getStatusCode();

        $this->assertTrue(
            $this->response->isOk(),
            "Expected status code 200, got {$actual}, response: {$this->getResponseShortContent()}."
        );

        return $this->response;
    }

    private function getResponseShortContent(): string
    {
        $content = $this->response->getContent();
        if (!$content) {
            return '(empty)';
        }
        $json = json_decode($content);
        if (json_last_error() === JSON_ERROR_NONE) {
            $content = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return "\n".Str::limit($content, 700);
    }

    // The Laravel/Lumen expectsJobs function is broken. See this issue:
    // https://github.com/laravel/lumen-framework/issues/416 open since May
    // 23, 2016. We'll implement our own nicer method for checking that jobs
    // have been executed.
    protected function fakeJobs()
    {
        $this->withoutJobs();
    }

    protected function expectsJobs($jobs)
    {
        throw new Exception('expectsJobs is unstable - avoid using it');
    }

    protected function assertJobsDispatched($jobs)
    {
        $this->doJobsDispatchedAssertion($jobs, 'assertContains');
    }

    protected function assertJobsNotDispatched($jobs)
    {
        $this->doJobsDispatchedAssertion($jobs, 'assertNotContains');
    }

    private function doJobsDispatchedAssertion($jobs, $assertion)
    {
        $jobs = is_array($jobs) ? $jobs : [$jobs];
        $dispatchedJobs = array_map('get_class', $this->dispatchedJobs ?? []);

        foreach ($jobs as $job) {
            $this->{$assertion}($job, $dispatchedJobs);
        }
    }

    protected function fakeMail()
    {
        $mailer = $this->app->make(MailFake::class);
        $this->app->instance(Mailer::class, $mailer);
    }

    protected function assertMailSent($email, callable $callable = null)
    {
        $this->app->make(Mailer::class)->assertSent($email, $callable);
    }

    protected function assertMailNotSent($email, callable $callable = null)
    {
        $this->app->make(Mailer::class)->assertNotSent($email, $callable);
    }

    protected function assertMailQueued($email, callable $callable = null)
    {
        $this->app->make(Mailer::class)->assertQueued($email, $callable);
    }

    protected function assertMailNotQueued($email, callable $callable = null)
    {
        $this->app->make(Mailer::class)->assertNotQueued($email, $callable);
    }
}
