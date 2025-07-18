<?php

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(ResponseFactory::class);
    },
];