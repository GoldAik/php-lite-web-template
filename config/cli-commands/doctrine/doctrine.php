<?php

declare(strict_types = 1);

use Doctrine\Migrations\DependencyFactory;

return fn(DependencyFactory $dependencyFactory) => array_merge(
    require_once __DIR__ . '/doctrine-custom.php',
    (require_once __DIR__ . '/doctrine-migration.php')($dependencyFactory),
);