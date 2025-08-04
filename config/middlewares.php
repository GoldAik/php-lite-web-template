<?php

declare(strict_types = 1);

use App\Config;
use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\ShutdownHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function(\Slim\App $app) {
    $container = $app->getContainer();
    $config = $container->get(Config::class);

    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

    $app->addRoutingMiddleware();

    $errorHandlerLogger = (new Logger('error-handler'))->pushHandler(new RotatingFileHandler(LOG_PATH . '/error-handler.log'));
    $shutdownHandlerlogger = (new Logger('shutdown-handler'))->pushHandler(new RotatingFileHandler(LOG_PATH . '/shutdown-handler.log'));

    $callableResolver = $app->getCallableResolver();
    $responseFactory = $app->getResponseFactory();

    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $errorHandlerLogger);
    $shutdownHandler = ShutdownHandler::make($request, $errorHandler, $shutdownHandlerlogger, $config['debug_mode']);
    register_shutdown_function($shutdownHandler);

    $errorMiddleware = $app->addErrorMiddleware(
        displayErrorDetails: $config['display_error_details'],
        logErrors: $config['log_errors'],
        logErrorDetails: $config['log_error_details'],
    );

    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};