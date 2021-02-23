<?php declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

$dotEnv = env('APP_ENV', null) === 'testing'
    ? Dotenv\Dotenv::create(dirname(__DIR__), '.env.example')
    : Dotenv\Dotenv::create(dirname(__DIR__));

$dotEnv->load();

// Make sure we have the environment variables
$dotEnv->required([
    'APP_NAME',
    'APP_ENV',
    'APP_DEBUG',
    'APP_TIMEZONE',
    'APP_URL',
    'APP_SCHEME',
    'APP_KEY',
    'API_DOCUMENTATION',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'CACHE_DRIVER',
    'WALLET_DRIVER',
    'DGSDB_HOST',
    'DGSDB_DATABASE',
    'DGSDB_USERNAME',
    'DGSDB_PASSWORD',
    'REDIS_HOST',
    'REDIS_PREFIX',
    'QUEUE_CONNECTION',
    'BEANSTALKD_HOST',
    'QUEUE_PREFIX',
    'REPORT_ERRORS',
    'CERBERUS_EXCEPTION_RECORD_URL',
]);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Load The Application Configs
|--------------------------------------------------------------------------
|
| Next we will include the configs file so that they can all be added to
| the application.
*/

$app->configure('app');
$app->configure('auth');
$app->configure('cache');
$app->configure('database');
$app->configure('elastic-apm');
$app->configure('elasticsearch');
$app->configure('logging');
$app->configure('queue');
$app->configure('services');
$app->configure('wallet');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(App\Services\ApmNotifier::class);
$app->singleton(App\Services\CerberusNotifier::class);
$app->singleton(App\Services\NewRelicNotifier::class);
$app->singleton(App\Support\Logger::class);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    App\Http\Middleware\IdentifierMiddleware::class,
    App\Http\Middleware\ApmMiddleware::class,
    App\Http\Middleware\CorsMiddleware::class,
    App\Http\Middleware\NewRelicMiddleware::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\AuthMiddleware::class,
    'logger' => App\Http\Middleware\LoggerMiddleware::class,
    'idem' => App\Http\Middleware\IdempotentMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Illuminate\Redis\RedisServiceProvider::class);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\ElasticServiceProvider::class);
$app->register(App\Providers\RequestServiceProvider::class);
$app->register(App\Providers\WalletServiceProvider::class);
$app->register(Sentry\Laravel\ServiceProvider::class);
$app->register(PhilKra\ElasticApmLaravel\Providers\ElasticApmServiceProvider::class);

if (env('APP_ENV', 'production') === 'local') {
    $app->register(Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
}

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group(['namespace' => 'App\Http\Controllers'], static function (Laravel\Lumen\Routing\Router $router) {
    require __DIR__.'/../routes/api.php';
    require __DIR__.'/../routes/heartbeat.php';

    if ((bool) env('API_DOCUMENTATION', false)) {
        require __DIR__.'/../routes/documentation.php';
    }
});

return $app;
