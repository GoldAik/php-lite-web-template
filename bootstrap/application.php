<?php

declare(strict_types = 1);

use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\ShutdownHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\Factory\ServerRequestCreatorFactory;

require_once __DIR__ . '/database.php';

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions(APP_PATH . '/bootstrap/container.php');
$container = $containerBuilder->build();

$app = \DI\Bridge\Slim\Bridge::create($container);

$app->addRoutingMiddleware();

$logger = new Logger('application');
$logger->pushHandler(new RotatingFileHandler(LOG_PATH . '/application.log'));

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $logger);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, DEBUG_MODE);
register_shutdown_function($shutdownHandler);

$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

$errorMiddleware->setDefaultErrorHandler($errorHandler);

return $app;