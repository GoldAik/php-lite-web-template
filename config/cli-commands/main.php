<?php

declare(strict_types = 1);

use Doctrine\Migrations\DependencyFactory;

return fn(DependencyFactory $dependencyFactory) => [
    ...((require_once CONFIG_PATH . '/cli-commands/doctrine/doctrine.php')($dependencyFactory)),
];