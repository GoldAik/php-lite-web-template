<?php

declare(strict_types = 1);

namespace App\Routes;

use Doctrine\ORM\EntityManager;

$userRoutes = require_once 'user.php';

return function (\Slim\App $app, $args = []) use ($userRoutes) {
    $enityManager = $args['entity_manager'] ?? null;
    if ($enityManager instanceof EntityManager) 
    {
        $cleanArgs = array_diff_key($args, ['entity_manager']);

        /**
         * Declaring routes that need EntityManager 
         */
        $userRoutes($app, $enityManager, $cleanArgs);
    }

    /**
     * Declaring routes that do not need EntityManager 
     */
    # --------- here ---------


    /**
     * Caching routes
     */
    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile(CACHE_PATH . '/routes.file');
};