<?php

declare(strict_types = 1);

namespace Test\Integration\Core;

use App\Config;
use App\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\Container\ContainerInterface;

#[CoversNothing]
class ContainerBindingsTest extends TestCase
{
    protected const PATH_OF_CONSTANT_PATH_FILE = __DIR__ . '/../../../config/path_constants.php';
    protected string $containerBindingsPath;
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;
        $this->containerBindingsPath = CONFIG_PATH . '/container/container_bindings.php';
        $env = Dotenv::createImmutable(APP_PATH);
        $env->load();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(require $this->containerBindingsPath);
        $this->container = $containerBuilder->build();
    }

    public function testContainerBuilds(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
    }

    public function testConfigIsResolved(): void
    {
        $config = $this->container->get(Config::class);
        $this->assertIsObject($config);
    }

    public function testSessionBinding(): void
    {
        $session = $this->container->get(SessionInterface::class);
        $this->assertInstanceOf(SessionInterface::class, $session);
    }
}