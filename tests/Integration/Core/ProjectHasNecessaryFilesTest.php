<?php

declare(strict_types = 1);

namespace Test\Integration\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
class ProjectHasNecessaryFilesTest extends TestCase
{
    protected const PATH_OF_CONSTANT_PATH_FILE = __DIR__ . '/../../../config/path_constants.php';

    protected array $requiredFiles;

    protected function setUp(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;

        $this->requiredFiles = [
            CONFIG_PATH . '/container/container_bindings.php',
            CONFIG_PATH . '/container/container.php',
            CONFIG_PATH . '/app.php',
            CONFIG_PATH . '/middlewares.php',
            // CONFIG_PATH . '/path_constants.php', # is tested in other specific Test Class
            CONFIG_PATH . '/cli-commands/main.php',
            SOURCE_PATH . '/Routes/main.php',
        ];
    }

    public function testFilesExistAndAccessible(): void
    {
        foreach ($this->requiredFiles as $path) {
            $this->assertIsString($path);
            $this->assertFileExists($path);
            $this->assertFileIsReadable($path);
        }
    }
}