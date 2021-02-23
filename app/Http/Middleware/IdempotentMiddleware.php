<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdempotentMiddleware
{
    /** @var Cache */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->buildCacheKey($request);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            $this->cache->put($key, $response, config('cache.idem_ttl'));
        }

        return $response;
    }

    protected function buildCacheKey(Request $request): string
    {
        $data = $request->all() + ['path' => $request->getPathInfo(), 'method' => $request->getMethod()];
        $data = array_unset($data, ['description']);

        $httpQuery = http_build_query($data);

        return hash_hmac('sha256', urldecode($httpQuery), env('APP_KEY'));
    }
}
