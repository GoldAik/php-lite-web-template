<?php

declare(strict_types = 1);

define('DEBUG_MODE', true);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';


$app = \DI\Bridge\Slim\Bridge::create();
$app->addRoutingMiddleware();

$app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$middleware = function (Request $req, RequestHandler $reqHandler) {
    $res = $reqHandler->handle($req);
    $res->getBody()->write(' - after middleware');
    return $res;
};

$app->get('/{name}', function (Response $response, $name) {
    $response->getBody()->write("Hello $name");
    return $response;
})->add($middleware);

$app->run();