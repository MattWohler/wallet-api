<?php declare(strict_types=1);

namespace App\Providers;

use App\Http\Requests\AbstractRequest as Request;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\Redirector;

class RequestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->afterResolving(ValidatesWhenResolved::class, static function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(Request::class, static function ($request, $app) {
            $request = Request::createFrom($app['request'], $request);
            $request->setContainer($app)->setRedirector($app->make(Redirector::class));

            if (method_exists($request, 'injectDependencies')) {
                $app->call([$request, 'injectDependencies']);
            }
        });
    }
}
