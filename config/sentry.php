<?php declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Sentry Integration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the sentry
    |
    */

    'dsn' => !in_array(env('APP_ENV'), ['development', 'production'], true)
        ? null
        : 'http://06cd4b1082514c21abaf862bee0ba016:60ef41da8d5b48698f5937ca96c8242c@sentry.jetu.cr/10',

    'release' => !file_exists(__DIR__.'/../RELEASE')
        ? null
        : trim((string) file_get_contents(__DIR__.'/../RELEASE')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture certain personally identifiable information
    'send_default_pii' => true,
];
