<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-Correlation-Id')) {
            $request->headers->set('X-Correlation-Id', $this->cuid());
        }

        $request->headers->set('X-Request-Id', $this->cuid());

        return $next($request);
    }

    protected function cuid(): string
    {
        /** @link http://usecuid.org/ - collision resistant id */
        return hash('sha384', mt_rand().microtime().uniqid('wallet_api', true));
    }
}
