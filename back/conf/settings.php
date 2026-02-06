<?php
return [
    'settings' => [
        'db' => [
            'dsn' => 'pgsql:host=' . ($_ENV['POSTGRES_HOST'] ?? 'localhost') . ';port=5432;dbname=' . ($_ENV['POSTGRES_DB'] ?? 'curioMap'),
            'user' => $_ENV['POSTGRES_USER'] ?? 'admin',
            'password' => $_ENV['POSTGRES_PASSWORD'] ?? 'admin',
        ],
        'jwt' => [
            'key' => 'clef',
            'algorithm' => 'HS256',
            'expiration' => 3600, //= 1h
        ],
        'cors' => [
            'origin' => '*',
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
            'headers' => ['Content-Type', 'Authorization'],
        ],
        'displayErrorDetails' => true,
        'logErrors' => true,
        'logErrorDetails' => true,
    ],
];