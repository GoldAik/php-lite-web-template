<?php

declare(strict_types = 1);

namespace Test\Figures\Routes;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;


return function (\Slim\App $app, $args = []) {
    $app->group('/blog/{name}', function (RouteCollectorProxy $group) use ($args) {
        $group->get('', function (Response $response, $name) {
            $response->getBody()->write("Hello on blog {$name}");
            return $response;
        });
        
        $group->get('/edit', function (Response $response, $name) {
            $response->getBody()->write("Here you can edit ur blog :)");
            return $response;
        });
    });
};