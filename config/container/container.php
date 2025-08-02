<?php

declare(strict_types = 1);

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions(CONFIG_PATH . '/container/container_bindings.php');
$container = $containerBuilder->build();

return $container;