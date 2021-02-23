<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Logger
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param  Request  $request
     * @param  JsonResponse|Response  $response
     */
    public function info($request, $response): void
    {
        $route = $request->route();
        $data = [
            'method' => $request->getMethod(),
            'endpoint' => $route[1]['as'] ?? $route[1]['uses'] ?? $request->path(),
            'request' => $request->all(),
            'response' => method_exists($response, 'getData')
                ? $response->getData(true)
                : [$response->getContent()],
        ];

        $this->logger->info((string) json_encode($data));
    }

    public function error(Throwable $exception): void
    {
        $data = [
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        $this->logger->warning((string) json_encode($data));

        if (method_exists($this->logger, 'channel')) {
            $this->logger
                ->channel('error')
                ->error(json_encode($data + ['trace' => $exception->getTraceAsString()]));
        }
    }

    public function query(string $sql, array $bindings, array $response, string $channel = 'dgs'): void
    {
        if (method_exists($this->logger, 'channel')) {
            $this->logger
                ->channel($channel)
                ->info(json_encode(compact('sql', 'bindings', 'response')));
        }
    }
}
