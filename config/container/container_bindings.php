<?php

declare(strict_types = 1);

use App\Config;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;

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
    Twig::class                     => fn (Config $config) => Twig::create(
        TEMPLETE_PATH, [
            'debug' => $config['twig']['debug_mode'],
            'cache' => $config['twig']['cache_dir'],
            'auto_load' => $config['twig']['auto_load'],
        ]
    ),
    ResponseFactoryInterface::class => fn (ContainerInterface $container) => $container->get(ResponseFactory::class),
];