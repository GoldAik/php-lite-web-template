<?php

declare(strict_types = 1);

namespace App\Routes;

use App\Config;


return function (\Slim\App $app, $args = []) {    
    /**
     * Declaring routes
     */
    (require_once 'user.php')($app, $args);


    /**
     * Caching routes
     */
$container = $app->getContainer();
    $debugMode = $container->get(Config::class)['debug_mode'];

    if (! $debugMode) {
    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile(CACHE_PATH . '/routes.file');
}
};