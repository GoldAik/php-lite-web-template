<?php

declare(strict_types = 1);

/**
 * Setting up important security error handling.
 * 
 * When in production, it's recommended to turn off display_errors in php.ini.
 */
error_reporting(E_ALL);
ini_set("display_errors", 0);

use App\Config;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/path_constants.php';

$env = Dotenv::createImmutable(APP_PATH);
$env->load();

$container = require CONFIG_PATH . '/container/container.php';
$app       = \DI\Bridge\Slim\Bridge::create($container);
$config    = $container->get(Config::class); 

ini_set("display_errors", (int) $config['debug_mode']);

$middlewares = require CONFIG_PATH . '/middlewares.php';
$middlewares($app);

return $app;