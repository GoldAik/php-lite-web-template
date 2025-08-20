<?php

declare(strict_types = 1);

namespace Test\Figures\Routes;

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


return function (\Slim\App $app, $args = []) {
    $app->group('/auth', function (RouteCollectorProxy $group) use ($args) {
        $group->get('/login', function (Response $response) {
            $response->getBody()->write("Login");
            return $response;
        });
        $group->post('/login', function (Response $response) {
            $response->getBody()->write("Login");
            return $response;
        });
        $group->get('/register', function (Response $response) {
            $response->getBody()->write("Register");
            return $response;
        });
        $group->get('/logout', function (Response $response) {
            $response->getBody()->write("Logout");
            return $response;
        });
        $group->get('/reset-password', function (Response $response) {
            $response->getBody()->write("Reset Password");
            return $response;
        });
    });
};