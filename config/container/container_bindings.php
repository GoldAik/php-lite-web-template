<?php

declare(strict_types = 1);

use App\Config;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;

use function DI\create;

return [
    Config::class                   => create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),
    EntityManager::class            => fn (Config $config) => new EntityManager(
        DriverManager::getConnection($config['doctrine']['connection'] ?? []),
        ORMSetup::createAttributeMetadataConfiguration(
            $config['doctrine']['entity_dir'] ?? [],
            $config['doctrine']['dev_mode'] ?? false
        )
    ),
    ResponseFactoryInterface::class => fn (ContainerInterface $container) => $container->get(ResponseFactory::class),
];