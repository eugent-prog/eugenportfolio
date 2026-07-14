<?php
declare(strict_types=1);

return [
    'site' => [
        'name' => 'Eugen',
        'email' => 'eugentaborada21@gmail.com',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'eugen_portfolio',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
