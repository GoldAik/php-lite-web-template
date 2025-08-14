<?php

declare(strict_types = 1);

namespace Test\Figures\Routes;

use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app, $args = []) {    
    /**
     * Declaring routes
     */
    $app->get('/', function (Response $response) {
        $response->getBody()->write("Hello World");
        return $response;
    });

    (require_once 'auth.php')($app, $args);
    (require_once 'blog.php')($app, $args);

    /**
     * Caching routes
     */
    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile(CACHE_PATH . '/routes.file');
};