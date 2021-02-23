<?php declare(strict_types=1);

namespace App\Services;

use Throwable;

class CerberusNotifier
{
    public function captureException(Throwable $exception): void
    {
        $params = $this->buildParams($exception);
        $header = $this->buildHttpHeader($params);

        $stream = stream_context_create($header);
        @file_get_contents(config('services.cerberus'), false, $stream);
    }

    protected function buildParams(Throwable $exception): array
    {
        return [
            'app' => 'casino_wallet_api',
            'server' => $_SERVER['SERVER_ADDR'] ?? 'localhost',
            'message' => 'endpoint : '.app('request')->path().' | message : '.$exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'trace' => $exception->getTraceAsString()
        ];
    }

    protected function buildHttpHeader(array $params): array
    {
        return [
            'http' => [
                'timeout' => 5,
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(['e' => json_encode($params)])
            ]
        ];
    }
}
