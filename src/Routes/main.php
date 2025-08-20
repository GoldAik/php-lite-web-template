<?php

declare(strict_types = 1);

namespace App\Routes;

use App\Config;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app, $args = []) {    
    /**
     * Declaring routes
     */
    $app->get('/', function (Response $response) {
        $response->getBody()->write("Hello World");
        return $response;
    });

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