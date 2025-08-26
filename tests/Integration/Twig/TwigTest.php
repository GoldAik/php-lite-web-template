<?php

declare(strict_types = 1);

namespace Test\Integration\Twig;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Views\Twig;


#[CoversNothing]
class TwigTest extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = require __DIR__ . "/../../../bootstrap.php";
    }

    public function testTwigInstanceExist(): void
    {
        /** @var Twig $twig */
        $twig = $this->app->getContainer()->get(Twig::class);
        $this->assertInstanceOf(Twig::class, $twig);
    }

    public function testTwigRenderStringTemplate(): void
    {
        $name = "Foo";

        /** @var Twig $twig */
        $twig = $this->app->getContainer()->get(Twig::class);
        $SUT = $twig->fetchFromString('<h1>Hello, {{ name }}!</h1>', ['name' => $name]);

        $this->assertStringContainsString("Hello, {$name}", $SUT);
    }

    public function testTemplateFileExist(): void
    {
        $templatePath = __DIR__ . '/../../Figures/Templates/basic.html.twig';
        $this->assertFileExists($templatePath);
    }

    public function testTwigRenderStringTemplateFromFile(): void
    {
        $name = "Foo";
        $templatePath = __DIR__ . '/../../Figures/Templates/basic.html.twig';
        $templateContent = file_get_contents($templatePath);

        /** @var Twig $twig */
        $twig = $this->app->getContainer()->get(Twig::class);
        $SUT = $twig->fetchFromString($templateContent, ['name' => $name]);

        $this->assertStringContainsString("Hello, {$name}", $SUT);
    }
}