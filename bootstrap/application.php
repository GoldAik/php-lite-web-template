<?php

declare(strict_types = 1);

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Psr7\Response as SlimResponse;

require_once __DIR__ . '/database.php';

$app = \DI\Bridge\Slim\Bridge::create();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new SlimResponse();
        $response->getBody()->write('404 NOT FOUND');

        return $response->withStatus(404);
    });

$errorMiddleware->setErrorHandler(
    HttpMethodNotAllowedException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new SlimResponse();
        $response->getBody()->write('405 NOT ALLOWED');

        return $response->withStatus(405);
    });

return $app;