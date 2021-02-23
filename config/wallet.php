<?php declare(strict_types=1);

use App\Services\Driver\Dgs\DgsService;

return [
    'default' => env('WALLET_DRIVER', 'dgs'),

    'services' => [
        'dgs' => [
            'class' => DgsService::class,
            'config' => [
                'connection' => 'dgsdb',
            ]
        ],
    ],
];
