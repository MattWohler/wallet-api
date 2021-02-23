<?php declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\HandledException;
use App\Exceptions\Handled\DuplicateTransactionException;
use App\Exceptions\Handled\InsufficientFundsException;
use App\Exceptions\Handled\UnknownRollbackTransactionException;
use App\Exceptions\Handled\WorkerException;
use App\Services\ApmNotifier;
use App\Services\CerberusNotifier;
use App\Services\NewRelicNotifier;
use App\Support\Logger;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /** @var Logger */
    protected $logger;

    /** @var array - A list of the exception types that should not be reported. */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        InsufficientFundsException::class,
        DuplicateTransactionException::class,
        UnknownRollbackTransactionException::class,
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        try {
            // Always log errors in file
            $this->logger->error($exception);
            // Do not call sentry, cerberus nor new relic on local
            if ((bool) config('logging.report_errors', false)) {
                app('sentry')->captureException($exception);
                app(ApmNotifier::class)->captureException($exception);
                app(CerberusNotifier::class)->captureException($exception);
                app(NewRelicNotifier::class)->captureException($exception);
            }
        } catch (Throwable $e) {
            // Error in the handler - use lumen emergency logging
            parent::report(new FatalThrowableError($e));
        }
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof HandledException) {
            return response()->json([
                'status' => $exception->getStatus(),
                'errors' => $exception->getErrors()
            ], $exception->getStatus() !== 2003 ? $exception->getStatus() : 500);
        }

        return $this->prepareJsonResponse($request, $exception);
    }

    public function prepareJsonResponse($request, Exception $exception)
    {
        $rendered = parent::render($request, $exception);
        $status = $rendered->getStatusCode();

        return response()->json([
            'status' => $status,
            'errors' => $exception->getMessage()
        ], $status !== 2003 ? $status : 500);
    }
}
