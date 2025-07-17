<?php

declare(strict_types = 1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/config.php';

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [ENTITY_PATH],
    isDevMode: DEBUG_MODE
);

$connection = DriverManager::getConnection([
    'driver' => $_ENV['DATABASE_DRIVER'],
    'dbname' => $_ENV['DATABASE_NAME'],
    'user' => $_ENV['DATABASE_USER'],
    'password' => $_ENV['DATABASE_PASSWORD'],
], $config);

$entityManager = new EntityManager($connection, $config);