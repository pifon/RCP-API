<?php

/*
    return [
        'connection' => [
            'driver'   => 'pdo_mysql', // Or your preferred driver
            'host'     => env('DB_HOST', '127.0.0.1'),
            'dbname'   => env('DB_DATABASE', 'database'),
            'user'     => env('DB_USERNAME', 'username'),
            'password' => env('DB_PASSWORD', 'password'),
            'charset'  => 'utf8mb4',
        ],

];
*/

declare(strict_types=1);

return [
    'managers' => [
        'default' => [
            'dev' => env('APP_DEBUG', false),
            'meta' => env('DOCTRINE_METADATA', 'attributes'),
            'connection' => env('DB_CONNECTION', 'mysql'),
            'paths' => [
                base_path('app/Entities'),
            ],

            'repository' => Doctrine\ORM\EntityRepository::class,

            'proxies' => [
                'namespace' => 'DoctrineProxies',
                'path' => storage_path('proxies'),
                'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', true),
            ],
            'events' => [
                'listeners' => [],
                'subscribers' => [],
            ],
            'filters' => [],
            'mapping_types' => [
                'enum' => 'string',
            ],
            'middlewares' => [
                // Doctrine\DBAL\Logging\Middleware::class
            ],
        ],
    ],
    'extensions' => [],
    'custom_types' => [],
    'custom_datetime_functions' => [],
    'custom_numeric_functions' => [],
    'custom_string_functions' => [],
    'custom_hydration_modes' => [],
    'cache' => [
        'second_level' => false,
        'default' => env('DOCTRINE_CACHE', 'array'),
        'namespace' => null,
        'metadata' => [
            'driver' => env('DOCTRINE_METADATA_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
        'query' => [
            'driver' => env('DOCTRINE_QUERY_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
        'result' => [
            'driver' => env('DOCTRINE_RESULT_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
    ],
    'gedmo' => [
        'all_mappings' => false,
    ],
    'doctrine_presence_verifier' => true,
    'notifications' => [
        'channel' => 'database',
    ],
];
