<?php

declare(strict_types = 1);

namespace App\Routes;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Views\Twig;

return function (\Slim\App $app, $args = []) {
    $app->group('/profile/{name}', function (RouteCollectorProxy $group) use ($args) {

        $group->get('', function (Request $request, Response $response, $name) {
            $twig = Twig::fromRequest($request);
            return $twig->render($response, 'profile.html.twig', ['name' => $name]);
        });
    });
};