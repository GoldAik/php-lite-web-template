<?php

declare(strict_types = 1);

namespace Test\Unit\Session;

use App\Session\DTO\Options;
use App\Session\Enum\SameSite;
use App\Session\Global\SessionGlobalFunctions;
use App\Session\Global\SessionGlobalFunctionsInterface;
use App\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Test\Unit\TestHelper;

use function PHPUnit\Framework\once;

#[CoversClass(Session::class)]
#[UsesClass(Options::class)]
class SessionTest extends TestCase
{
    private Options $options;

    protected function setUp(): void
    {
        $this->options = new Options(
            name: 'my_session',
            lifetime: 3600,
            path: '/app',
            secure: false,
            httpOnly: false,
            sameSite: SameSite::Strict,
            storagePath: '/tmp/sessions'
        );
    }

    #[Group('constructor')]
    #[DataProvider('sessionOptionsDataProvider')]
    public function testConstructorOptions(Options $options): void
    {
        $session = new Session($options);

        $optionsReflection = TestHelper::getProperty($session, 'options');

        $this->assertSame($options->name, $optionsReflection->name);
        $this->assertSame($options->lifetime, $optionsReflection->lifetime);
        $this->assertSame($options->path, $optionsReflection->path);
        $this->assertSame($options->secure, $optionsReflection->secure);
        $this->assertSame($options->httpOnly, $optionsReflection->httpOnly);
        $this->assertSame($options->sameSite, $optionsReflection->sameSite);
        $this->assertSame($options->storagePath, $optionsReflection->storagePath);
    }

    public function testConstructorDefaultGlobalFunction(): void
    {
        $session = new Session($this->options);
        $functions = TestHelper::getProperty($session, 'functions');

        $this->assertInstanceOf(SessionGlobalFunctions::class, $functions);
    }

    public function testConstructorCustomGlobalFunction(): void
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $session = new Session($this->options, $sessionGlobalFunctionsMock);

        $functions = TestHelper::getProperty($session, 'functions');

        $this->assertSame($sessionGlobalFunctionsMock, $functions);
    }

    public function testStartThrowRuntimeExceptionWhenSessionActive(): void
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $sessionGlobalFunctionsMock->method('sessionStatus')->willReturn(PHP_SESSION_ACTIVE);

        $session = new Session($this->options, $sessionGlobalFunctionsMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session has already been started');

        $session->start();
    }

    public function testStartThrowRuntimeExceptionWhenHeaderSent(): void
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $sessionGlobalFunctionsMock->method('headersSent')->willReturn(true);

        $session = new Session($this->options, $sessionGlobalFunctionsMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Headers already sent');

        $session->start();
    }

    public function testStartCorrectly(): void
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionStatus');
        $sessionGlobalFunctionsMock->expects($this->once())->method('headersSent');
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionSetCookieParams');
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionSavePath');
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionStart');

        $session = new Session($this->options, $sessionGlobalFunctionsMock);

        $session->start();
    }

    public function testSaveCallsSessionWriteClose()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionWriteClose');

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $session->save();
    }

    public function testRegenerateCallsSessionRegenerateId()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);
        $sessionGlobalFunctionsMock->expects($this->once())->method('sessionRegenerateId');

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $session->regenerate();
    }


    public function testGetReturnsValueWhenExists()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);

        $_SESSION['foo'] = 'bar';

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $value = $session->get('foo', 'default');

        $this->assertEquals('bar', $value);
    }

    public function testGetReturnsDefaultWhenNotExists()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);

        $_SESSION = [];

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $default = 'default_value';
        $value = $session->get('nonexistent', $default);

        $this->assertEquals($default, $value);
    }

    public function testSetStoresValueInSession()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);

        $_SESSION = [];

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $session->set('key', 'value');

        $this->assertSame('value', $_SESSION['key']);
    }

    public function testUnsetRemovesKey()
    {
        $sessionGlobalFunctionsMock = $this->createMock(SessionGlobalFunctionsInterface::class);

        $_SESSION['foo'] = 'bar';
        $_SESSION['foobar'] = 'barbar';

        $session = new Session($this->options, $sessionGlobalFunctionsMock);
        $session->unset('foo');

        $this->assertArrayNotHasKey('foo', $_SESSION);
        $this->assertArrayHasKey('foobar', $_SESSION);
    }

    public static function sessionOptionsDataProvider(): array
    {
        return [
            [
                new Options(...[
                    "name" => 'my_session',
                    "lifetime" => 3600,
                    "path" => '/app',
                    "secure" => false,
                    "httpOnly" => false,
                    "sameSite" => SameSite::Strict,
                    "storagePath" => '/tmp/sessions'
                ])
            ],
            [
                new Options(...[
                    "name" => 'default_session',
                    "lifetime" => 60 * 30,
                    "path" => '/',
                    "secure" => true,
                    "httpOnly" => true,
                    "sameSite" => SameSite::Lax,
                    "storagePath" => null
                ])
            ],
            [
                new Options(...[
                    "name" => 'custom_path_session',
                    "lifetime" => 7200,
                    "path" => '/custom',
                    "secure" => true,
                    "httpOnly" => true,
                    "sameSite" => SameSite::None,
                    "storagePath" => '/var/sessions'
                ])
            ],
            [
                new Options(...[
                    "name" => 'less_secure_session',
                    "lifetime" => 1800,
                    "path" => '/',
                    "secure" => false,
                    "httpOnly" => false,
                    "sameSite" => SameSite::Strict,
                    "storagePath" => null
                ])
            ],
            [
                new Options(...[
                    "name" => 'samesite_none',
                    "lifetime" => 3600,
                    "path" => '/',
                    "secure" => true,
                    "httpOnly" => true,
                    "sameSite" => SameSite::None,
                    "storagePath" => '/tmp'
                ])
            ],
        ];
    }

}