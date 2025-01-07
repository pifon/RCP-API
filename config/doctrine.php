<?php

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
