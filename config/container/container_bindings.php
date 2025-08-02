<?php

declare(strict_types = 1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [
    'config' => fn () => require CONFIG_PATH . '/app.php',
    EntityManager::class => function($container) {
        $config = $container->get('config');
        return new EntityManager(
            DriverManager::getConnection($config['doctrine']['connection'] ?? []),
            ORMSetup::createAttributeMetadataConfiguration(
                $config['doctrine']['entity_dir'] ?? [],
                $config['doctrine']['dev_mode'] ?? false
            )
        );
    },
    ResponseFactoryInterface::class => fn (ContainerInterface $container) => $container->get(ResponseFactory::class),
];