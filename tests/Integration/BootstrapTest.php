<?php

declare(strict_types = 1);

namespace Test\Integration\Core;

use App\Config;
use App\Session\Session;
use App\Session\SessionInterface;
use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;

#[CoversNothing]
class BootstrapTest extends TestCase
{
    protected const PATH_OF_CONSTANT_PATH_FILE = __DIR__ . '/../../config/path_constants.php';
    protected const BOOTSTRAP_PATH = __DIR__ . '/../../bootstrap.php';

    protected function setUp(): void
    {
        require_once self::PATH_OF_CONSTANT_PATH_FILE;
    }

    public function testBootstrapReturnsAppInstance()
    {
        $app = require self::BOOTSTRAP_PATH;
        $this->assertInstanceOf(App::class, $app);
    }

    public function testContainerHasExpectedServices()
    {
        $app = require self::BOOTSTRAP_PATH;
        $container = $app->getContainer();

        $this->assertTrue($container->has(Config::class));
        $this->assertTrue($container->has(Session::class));
        $this->assertTrue($container->has(SessionInterface::class));
        $this->assertTrue($container->has(ResponseFactoryInterface::class));
    }

    public function testCreateContainer(): void
    {
        $env = Dotenv::createImmutable(APP_PATH);
        $env->load();

        $container = require CONFIG_PATH . '/container/container.php';
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testCreateSlimAppFromContainer(): void
    {
        $env = Dotenv::createImmutable(APP_PATH);
        $env->load();

        $container = require CONFIG_PATH . '/container/container.php';
        $app = \DI\Bridge\Slim\Bridge::create($container);
        $this->assertInstanceOf(App::class, $app);
    }

    public function testDebugModeConfiguration()
    {
        $app = require self::BOOTSTRAP_PATH;
        $container = $app->getContainer();

        $config = $container->get(Config::class);
        $debugMode = $config['debug_mode'];

        $iniDisplayErrors = ini_get('display_errors');
        $this->assertEquals((int) $debugMode, (int) $iniDisplayErrors);
    }

    public function testApplayMiddlewares(): void
    {
        $this->expectNotToPerformAssertions();

        $env = Dotenv::createImmutable(APP_PATH);
        $env->load();

        $container = require CONFIG_PATH . '/container/container.php';
        $app = \DI\Bridge\Slim\Bridge::create($container);

        $middlewares = require CONFIG_PATH . '/middlewares.php';
        $middlewares($app);
    }
}