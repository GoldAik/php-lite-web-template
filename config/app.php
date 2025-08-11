<?php

declare(strict_types = 1);

$debugMode = (bool) ($_ENV['DEBUG_MODE'] ?? 0);
$sessionName = preg_replace('/[^a-zA-Z0-9_]/', '_', $_ENV['APP_NAME']);

return [
    'app_name'              => $_ENV['APP_NAME'],
    'app_version'           => $_ENV['APP_VERSION'] ?? '1.0',
    'debug_mode'            => $debugMode,
    'display_error_details' => $debugMode,
    'log_errors'            => true,
    'log_error_details'     => true,
    'session'               => [
        'name'        => $sessionName,
        'lifetime'   => 60 * 60 * 2,
        'path'        => '/',  
        'secure'      => true,
        'http_only'   => true,
        'same_site'   => 'lax',
        'storage_dir' => null, // Null indicates default storage directory
    ],
];
