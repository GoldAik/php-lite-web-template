<?php

declare(strict_types = 1);

namespace Test\Figures\Routes;

return function (\Slim\App $app, $args = []) {    
    /**
     * Declaring routes
     */
    (require_once 'user.php')($app, $args);


    /**
     * Caching routes
     */
    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile(CACHE_PATH . '/routes.file');
};