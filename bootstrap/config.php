<?php

declare(strict_types = 1);

define('DEBUG_MODE', true);
define('APP_PATH', __DIR__ . '/..');
define('SOURCE_PATH', __DIR__ . '/../src');
define('ENTITY_PATH', SOURCE_PATH . '/entity');

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$env = Dotenv::createImmutable(APP_PATH);
$env->load();