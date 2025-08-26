<?php

declare(strict_types = 1);

use App\Config;
use App\Session\DTO\Options as SessionOptions;
use App\Session\Enum\SameSite;
use App\Session\Session;
use App\Session\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;

use function DI\create;

return [
    Config::class                   => create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),
    Twig::class                     => fn (Config $config) => Twig::create(
        TEMPLETE_PATH, [
            'debug' => $config['twig']['debug_mode'],
            'cache' => $config['twig']['cache_dir'],
            'auto_load' => $config['twig']['auto_load'],
        ]
    ),
    Session::class                  => fn (Config $config) => new Session(
        new SessionOptions(
            name: $config['session']['name'],
            lifetime: $config['session']['lifetime'],
            path: $config['session']['path'],
            secure: $config['session']['secure'],
            httpOnly: $config['session']['http_only'],
            sameSite: SameSite::tryFrom($config['session']['same_site']) ?? SameSite::Strict,
            storagePath: $config['session']['storage_dir'],
        )
    ),
    SessionInterface::class         => fn (ContainerInterface $container) => $container->get(Session::class),
    ResponseFactoryInterface::class => fn (ContainerInterface $container) => $container->get(ResponseFactory::class),
];