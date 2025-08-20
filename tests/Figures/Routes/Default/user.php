<?php

declare(strict_types = 1);

namespace Test\Figures\Routes;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


return function (\Slim\App $app, $args = []) {
    $app->group('/profile/{name}', function (RouteCollectorProxy $group) use ($args) {

        $group->get('', function (Response $response, $name) {
            $response->getBody()->write("Hello {$name}");
            return $response;
        });
    });
};