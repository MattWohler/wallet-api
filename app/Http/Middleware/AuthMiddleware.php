<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /** @var Auth - The authentication guard factory instance. */
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        if ($this->auth->guard($guard)->guest()) {
            return response()->json([], 401);
        }

        return $next($request);
    }
}
