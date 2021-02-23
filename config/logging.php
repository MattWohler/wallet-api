<?php declare(strict_types=1);

use App\Support\ElasticSearchHandler;
use Carbon\Carbon;
use Monolog\Logger as MonologLogger;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'file'),

    'report_errors' => (bool) env('REPORT_ERRORS', true) && env('APP_ENV') !== 'local',

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'error', 'sentry', 'elasticsearch'],
        ],

        'file' => [
            'driver' => 'stack',
            'channels' => ['daily', 'error', 'sentry', 'elasticsearch'],
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'dgs' => [
            'driver' => 'daily',
            'path' => storage_path('logs/dgs.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/error.log'),
            'level' => 'error',
            'days' => 14,
        ],

        'sentry' => [
            'driver' => 'sentry',
            'level' => MonologLogger::CRITICAL,
            'bubble' => true, // Whether the messages that are handled can bubble up the stack or not
        ],

        'elasticsearch' => [
            'driver' => 'monolog',
            'handler' => ElasticSearchHandler::class,
            'formatter' => 'default',
            'with' => [
                'options' => [
                    '@timestamp' => Carbon::now()->toIso8601ZuluString(),
                    '@version' => 1,
                    'index' => 'application_logs-'.Carbon::now()->format('Y.m.d'),
                    'type' => '_doc',
                    'score' => 1.0,
                    'labels' => [
                        'application' => env('APP_NAME', 'WalletAPI'),
                        'env' => env('APP_ENV', 'production'),
                    ],
                    'agent' => [
                        'name' => 'monolog',
                        'type' => 'app-logs',
                    ],
                    'host' => [
                        'hostname' => env('APP_URL', ''),
                        'type' => 'hector',
                    ],
                    'server' => [
                        'address' => $_SERVER['SERVER_ADDR'] ?? '',
                        'domain' => $_SERVER['SERVER_ADDR'] ?? '',
                    ],
                    'log' => [
                        'level' => 100, // default
                        'level_name' => 'DEBUG', // default
                    ]
                ]
            ]
        ],
    ],

];
