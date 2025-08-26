<?php

declare(strict_types = 1);

/**
 * @var Slim\App $app
 */
$app = require_once __DIR__ . '/../bootstrap.php';

$routes = require_once SOURCE_PATH . '/Routes/main.php';
$routes($app);

$app->run();