<?php

declare(strict_types = 1);

namespace Test\Integration;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Slim\App;

#[CoversNothing]
class RunTest extends TestCase
{
    protected const BOOTSTRAP_PATH = __DIR__ . '/../../bootstrap.php';
    protected const ROUTES_FIGURES_PATH = __DIR__ . '/../Figures/Routes';

    public function testBootstapingApp(): void
    {
        $app = require self::BOOTSTRAP_PATH;
        $this->assertInstanceOf(App::class, $app);
    }

    public function testRegisterDefaultRoutes(): void
    {
        $app = require self::BOOTSTRAP_PATH;
        $routes = require self::ROUTES_FIGURES_PATH . '/Default/main.php';
        $routes($app);

        $expectedPatterns = ['/profile/{name}'];
        $expectedCount = count($expectedPatterns);

        $routes = $app->getRouteCollector()->getRoutes();
        $this->assertCount($expectedCount, $routes);

        $patterns = array_map(fn ($route) => $route->getPattern(), $routes);

        $this->assertCount(count($expectedPatterns), $patterns);

        $patternsDiff = array_diff($expectedPatterns, $patterns);
        $this->assertSame([], $patternsDiff);
    }

    public function testRegisterEmptyRoutes(): void
    {
        $app = require self::BOOTSTRAP_PATH;
        $routes = require self::ROUTES_FIGURES_PATH . '/Empty/main.php';
        $routes($app);

        $expectedPatterns = [];
        $expectedCount = count($expectedPatterns);

        $routes = $app->getRouteCollector()->getRoutes();
        $this->assertCount($expectedCount, $routes);

        $patterns = array_map(fn ($route) => $route->getPattern(), $routes);

        $this->assertCount(count($expectedPatterns), $patterns);

        $patternsDiff = array_diff($expectedPatterns, $patterns);
        $this->assertSame([], $patternsDiff);
    }

    public function testRegisterCustomRoutes(): void
    {
        $app = require self::BOOTSTRAP_PATH;
        $routes = require self::ROUTES_FIGURES_PATH . '/Custom/main.php';
        $routes($app);

        $expectedPatterns = [
            '/',
            '/auth/login', // GET
            '/auth/login', // POST
            '/auth/register',
            '/auth/logout',
            '/auth/reset-password',
            '/blog/{name}',
            '/blog/{name}/edit',
        ];
        $expectedCount = count($expectedPatterns);

        $routes = $app->getRouteCollector()->getRoutes();
        $this->assertCount($expectedCount, $routes);

        $patterns = array_map(fn ($route) => $route->getPattern(), $routes);
        $this->assertCount(count($expectedPatterns), $patterns);

        $patternsDiff = array_diff($expectedPatterns, $patterns);
        $this->assertSame([], $patternsDiff);
    }
}