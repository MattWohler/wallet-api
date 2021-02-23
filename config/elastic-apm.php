<?php declare(strict_types=1);

return [
    // Sets whether the apm reporting should be active or not
    'active' => (bool) env('APM_ACTIVE', true) && in_array(env('APP_ENV'), ['production', 'local'], true),

    'app' => [
        // The app name that will identify your app in Kibana / Elastic APM
        'appName' => env('APP_NAME', 'WalletAPI'),

        // The version of your app
        'appVersion' => 'v2',
    ],

    'env' => [
        // whitelist environment variables
        'env' => ['SERVER_NAME', 'SERVER_ADDR', 'HTTP_HOST', 'REMOTE_ADDR'],

        // Application environment
        'environment' => env('APP_ENV', 'production'),
    ],

    // GuzzleHttp\Client options (http://docs.guzzlephp.org/en/stable/request-options.html#request-options)
    'httpClient' => [
        'headers' => [
            'Authorization' => 'Bearer '.env('APM_TOKEN', '')
        ],
    ],

    'server' => [
        // The apm-server to connect to
        'serverUrl' => env('ELASTIC_HOST').':'.env('APM_PORT', '8200'),

        // Token for x
        'secretToken' => env('APM_TOKEN', ''),

        // API version of the apm agent you connect to
        'apmVersion' => 'v1',

        // Hostname of the system the agent is running on.
        'hostname' => gethostname(),
    ],

    'spans' => [
        // Depth of backtraces
        'backtraceDepth' => env('BACKTRACE_DEPTH', 25),

        // Add source code to span
        'renderSource' => env('RENDER_SOURCE', true),

        'querylog' => [
            // Set to false to completely disable query logging, or to 'auto' if you would like to use the threshold feature.
            'enabled' => env('QUERY_LOG', true),

            // If a query takes longer then 200ms, we enable the query log. Make sure you set enabled = 'auto'
            'threshold' => env('THRESHOLD', 200),
        ],
    ],
];
