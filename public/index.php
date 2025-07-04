<?php

declare(strict_types = 1);

define('DEBUG_MODE', true);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$middleware = function (Request $req, RequestHandler $reqHandler) use ($app) {
    $res = $reqHandler->handle($req);
    $res->getBody()->write('after');
    return $res;
};

$app->get('/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello $name");
    return $response;
})->add($middleware);

$app->run();