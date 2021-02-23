<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewRelicMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (extension_loaded('newrelic') && env('APP_ENV') !== 'local') {
            $route = $request->route();
            newrelic_name_transaction($route[1]['as'] ?? $route[1]['uses'] ?? 'index.php');
        }

        return $response;
    }
}
