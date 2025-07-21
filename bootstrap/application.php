<?php

declare(strict_types = 1);

use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\ShutdownHandler;
use Slim\Factory\ServerRequestCreatorFactory;

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

return $app;