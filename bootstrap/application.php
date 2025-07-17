<?php

declare(strict_types = 1);

$connection = require_once __DIR__ . '/database.php';

$app = \DI\Bridge\Slim\Bridge::create();
$app->addRoutingMiddleware();

$app->addErrorMiddleware(
    displayErrorDetails: DEBUG_MODE,
    logErrors: true,
    logErrorDetails: true,
);

return $app;