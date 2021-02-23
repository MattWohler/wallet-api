<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Logger;
use Closure;
use Illuminate\Http\Request;
use PhilKra\Agent;
use PhilKra\Events\Metricset;
use PhilKra\Events\Span;
use PhilKra\Events\Transaction;
use PhilKra\Helper\Timer;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApmMiddleware
{
    /** @var Agent */
    protected $agent;

    /** @var Timer */
    protected $timer;

    /** @var Logger */
    protected $logger;

    public function __construct(Agent $agent, Timer $timer, Logger $logger)
    {
        $this->agent = $agent;
        $this->timer = $timer;
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!(bool) config('elastic-apm.active')) {
            return $next($request);
        }

        $transaction = $this->agent->startTransaction('');

        $transaction->setParentId((string) $request->header('X-Correlation-Id'));
        $transaction->setTraceId((string) $request->header('X-Request-Id'));

        $response = $next($request);

        $this->setResponse($transaction, $response);
        $this->setMeta($transaction, $response);

        $this->setUserContext($transaction, $request);
        $this->setTransactionName($transaction, $request);

        $this->setContexts($transaction);
        $this->setMetrics();

        $transaction->stop((int) $this->timer->getElapsedInMilliseconds());

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            $this->agent->send();
        } catch (Throwable $exception) {
            $this->logger->error($exception);
        }
    }

    protected function setResponse(Transaction $transaction, Response $response): void
    {
        $transaction->setResponse([
            'finished' => true,
            'headers_sent' => true,
            'status_code' => $response->getStatusCode(),
            'headers' => $this->formatHeaders($response->headers->all()),
        ]);
    }

    protected function formatHeaders(array $headers): array
    {
        return collect($headers)->map(static function ($values, $header) {
            return head($values);
        })->toArray();
    }

    protected function setMeta(Transaction $transaction, Response $response): void
    {
        $transaction->setMeta([
            'result' => $response->getStatusCode(),
            'type' => 'HTTP'
        ]);
    }

    protected function setUserContext(Transaction $transaction, Request $request): void
    {
        $user = $request->user();
        $transaction->setUserContext([
            'id' => optional($user)->id,
            'email' => optional($user)->email,
            'username' => optional($user)->user_name,
            'ip' => $request->ip(),
            'user-agent' => $request->userAgent(),
        ]);
    }

    protected function setTransactionName(Transaction $transaction, Request $request): void
    {
        $route = $request->route();
        $transaction->setTransactionName($route[1]['as'] ?? $route[1]['uses'] ?? $request->path());
    }

    protected function setMetrics(): void
    {
        $this->agent->putEvent(new Metricset([
            'system.process.cpu.total.norm.pct' => min(sys_getloadavg()[0] / 100, 1),
            'system.process.memory.used.pct' => min(memory_get_usage(true) / memory_get_usage(false), 1)
        ]));
    }

    protected function setContexts(Transaction $transaction): void
    {
        app('query-log')->each(function ($query) use ($transaction) {
            $span = new Span((string) array_get($query, 'name'), $transaction);
            $span->start();

            $span->setType((string) array_get($query, 'type'));
            $span->setContext(array_get($query, 'context', []));

            $span->setStacktrace(collect(array_get($query, 'stacktrace', []))->toArray());
            $span->stop((int) array_get($query, 'duration'));

            $this->agent->putEvent($span);
        });
    }
}
