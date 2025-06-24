<?php

declare(strict_types = 1);

define('DEBUG_MODE', true);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();