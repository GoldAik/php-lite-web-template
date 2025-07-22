<?php

declare(strict_types = 1);

/**
 * Setting up important security error handling.
 * 
 * DEBUG_MODE = false indicates production environment.
 * When in production, it's recommended to turn off display_errors in php.ini.
 */
define('DEBUG_MODE', true);

error_reporting(E_ALL);
ini_set("display_errors", (int) DEBUG_MODE);

define('APP_PATH', __DIR__ . '/..');
define('SOURCE_PATH', __DIR__ . '/../src');
define('ENTITY_PATH', SOURCE_PATH . '/entity');
define('CACHE_PATH', APP_PATH . '/_cache');
define('LOG_PATH', APP_PATH . '/logs');

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$env = Dotenv::createImmutable(APP_PATH);
$env->load();