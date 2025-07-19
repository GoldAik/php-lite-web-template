<?php

declare(strict_types = 1);

use Doctrine\ORM\EntityManager;

/**
 * @var Slim\App $app
 */
$app = require_once __DIR__ . '/../bootstrap/application.php';

/**
 * @var EntityManager $entityManager
 */
$routes = require_once SOURCE_PATH . '/Routes/main.php';
$routes($app, ['entity_manager' => $entityManager]);

$app->run();