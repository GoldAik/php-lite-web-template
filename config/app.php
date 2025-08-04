<?php

declare(strict_types = 1);

$debugMode = (bool) ($_ENV['DEBUG_MODE'] ?? 0);

return [
    'app_name'              => $_ENV['APP_NAME'],
    'app_version'           => $_ENV['APP_VERSION'] ?? '1.0',
    'debug_mode'            => $debugMode,
    'display_error_details' => $debugMode,
    'log_errors'            => true,
    'log_error_details'     => true,
    'doctrine'              => [
        'dev_mode'   => $debugMode,
        'cache_dir'  =>  CACHE_PATH . '/doctrine',
        'entity_dir' => [ENTITY_PATH],
        'connection' => [
            'driver'   => $_ENV['DATABASE_DRIVER'] ?? 'pdo_mysql',
            'host'     => $_ENV['DATABASE_HOST'] ?? 'localhost',
            'port'     => $_ENV['DATABASE_PORT'] ?? 3306,
            'dbname'   => $_ENV['DATABASE_NAME'],
            'user'     => $_ENV['DATABASE_USER'],
            'password' => $_ENV['DATABASE_PASSWORD'],
        ],
    ],
    'twig'                  => [
        'debug_mode' => $debugMode,
        'auto_load'  => $debugMode,
        'cache_dir'  => CACHE_PATH . '/twig',
    ],
];
