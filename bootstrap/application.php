<?php

declare(strict_types = 1);

use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\ShutdownHandler;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Response as SlimResponse;

require_once __DIR__ . '/database.php';

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions(APP_PATH . '/bootstrap/container.php');
$container = $containerBuilder->build();

$app = \DI\Bridge\Slim\Bridge::create($container);

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, DEBUG_MODE);
register_shutdown_function($shutdownHandler);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$errorMiddleware->setDefaultErrorHandler($errorHandler);

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