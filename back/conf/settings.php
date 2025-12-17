<?php
return [
    'settings' => [
        'db' => [
            'dsn' => 'pgsql:host=curiomap.db;port=5432;dbname=curioMap',
            'user' => 'admin',
            'password' => 'admin',
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