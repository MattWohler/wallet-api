<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Logger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoggerMiddleware
{
    /** @var Logger */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $this->logger->info($request, $response);

        return $response;
    }
}
