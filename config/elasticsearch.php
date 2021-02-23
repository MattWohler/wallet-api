<?php declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | ElasticSearch Integration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the elasticsearch
    |
    */

    'host' => env('ELASTIC_HOST'),

    'port' => env('ELASTIC_PORT', '9200'),

    'transport' => 'http',

    'username' => env('ELASTIC_USERNAME'),

    'password' => env('ELASTIC_PASSWORD'),
];
