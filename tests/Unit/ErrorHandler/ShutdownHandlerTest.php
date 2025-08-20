<?php

declare(strict_types = 1);

namespace Test\Unit\ErrorHandler;

use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\ShutdownHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Test\Unit\TestHelper;

#[CoversClass(ShutdownHandler::class)]
class ShutdownHandlerTest extends TestCase
{
    private $requestMock;
    private $errorHandlerMock;
    private $loggerMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(ServerRequestInterface::class);
        $this->errorHandlerMock = $this->createMock(HttpErrorHandler::class);
        $this->loggerMock = $this->createMock(Logger::class);
    }

    public function testConstructorWithDefaultFlags()
    {
        $handler = new ShutdownHandler(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock
        );

        $property = TestHelper::getProperty($handler, 'ignoreErrorsOnDisplayDetails');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrors');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrorDetails');
        $this->assertFalse($property);
    }

    public function testConstructorWithIgnoreErrorsFlag()
    {
        $flags = ShutdownHandler::IGNORE_ERRORS_ON_DISPLAY_DETAILS;
        $handler = new ShutdownHandler(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock,
            ignoreErrors: E_NOTICE,
            flags: $flags
        );

        $property = TestHelper::getProperty($handler, 'ignoreErrorsOnDisplayDetails');
        $this->assertTrue($property);

        $property = TestHelper::getProperty($handler, 'logErrors');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrorDetails');
        $this->assertFalse($property);
    }

    public function testConstructorWithLogErrorsFlag()
    {
        $flags = ShutdownHandler::LOG_ERRORS;
        $handler = new ShutdownHandler(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock,
            ignoreErrors: 0,
            flags: $flags
        );

        $property = TestHelper::getProperty($handler, 'ignoreErrorsOnDisplayDetails');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrors');
        $this->assertTrue($property);

        $property = TestHelper::getProperty($handler, 'logErrorDetails');
        $this->assertFalse($property);
    }

    public function testConstructorWithLogErrorDetailsFlag()
    {
        $flags = ShutdownHandler::LOG_ERROR_DETAILS;
        $handler = new ShutdownHandler(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock,
            ignoreErrors: 0,
            flags: $flags
        );

        $property = TestHelper::getProperty($handler, 'ignoreErrorsOnDisplayDetails');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrors');
        $this->assertFalse($property);

        $property = TestHelper::getProperty($handler, 'logErrorDetails');
        $this->assertTrue($property);
    }

        public function testMakeWithDebugModeEnabled()
    {
        $handler = ShutdownHandler::make(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock,
            debugMode: true
        );

        $flags = TestHelper::getProperty($handler, 'flags');

        $this->assertSame(ShutdownHandler::DISPLAY_ERROR_DETAILS, ($flags & ShutdownHandler::DISPLAY_ERROR_DETAILS));

        $this->assertSame(0, ($flags & ShutdownHandler::LOG_ERRORS));
        $this->assertSame(0, ($flags & ShutdownHandler::LOG_ERROR_DETAILS));
    }

    public function testMakeWithDebugModeDisabled()
    {
        $handler = ShutdownHandler::make(
            $this->requestMock,
            $this->errorHandlerMock,
            $this->loggerMock,
            debugMode: false
        );

        $flags = TestHelper::getProperty($handler, 'flags');

        $this->assertSame(0, ($flags & ShutdownHandler::DISPLAY_ERROR_DETAILS));

        $this->assertSame(ShutdownHandler::LOG_ERRORS, ($flags & ShutdownHandler::LOG_ERRORS));
        $this->assertSame(ShutdownHandler::LOG_ERROR_DETAILS, ($flags & ShutdownHandler::LOG_ERROR_DETAILS));
    }
}