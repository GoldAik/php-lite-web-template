<?php

declare(strict_types = 1);

namespace Test\Integration\Core;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
class PathConstantsTest extends TestCase
{
    protected const PATH_OF_CONSTANT_PATH_FILE = __DIR__ . '/../../../config/path_constants.php';

    protected const REQIURED_PATHS = [
        'APP_PATH',
        'SOURCE_PATH',
        'CACHE_PATH',
        'LOG_PATH',
        'CONFIG_PATH',
    ];

    public function testPathConstantFileExist(): void
    {
        $this->assertFileExists(self::PATH_OF_CONSTANT_PATH_FILE);
    }

    public function testPathConstantsConstainNecessaryPaths(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;

        foreach (self::REQIURED_PATHS as $constant) {
            $path = constant($constant);
            $this->assertIsString($path);
        }
    }

    public function testDirectoryOfPathExist(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;

        foreach (self::REQIURED_PATHS as $constant) {
            $path = constant($constant);
            $this->assertDirectoryExists($path);
        }
    }

    public function testPhpHasAccessToDirectoryPath(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;

        foreach (self::REQIURED_PATHS as $constant) {
            $path = constant($constant);
            $this->assertTrue(is_readable($path), "PHP does NOT have read access to {$path}.");
            $this->assertTrue(is_writable($path), "PHP does NOT have write access to {$path}.");
        }
    }
}