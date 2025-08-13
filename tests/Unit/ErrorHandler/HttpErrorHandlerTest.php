<?php

declare(strict_types = 1);

namespace Test\Unit\ErrorHandler;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\Enums\HttpErrorTypes;
use App\ErrorHandlers\ErrorRenderer\ErrorRendererInterface;
use App\ErrorHandlers\ErrorRenderer\JsonErrorRenderer;
use App\ErrorHandlers\HttpErrorHandler;
use App\ErrorHandlers\Logger\ErrorMapper;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpSpecializedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Interfaces\CallableResolverInterface;
use Test\Unit\TestHelper;
use Throwable;

class TestableHttpErrorHandler extends HttpErrorHandler
{
    public const TESTABLE_DEFAULT_TYPE = self::DEFAULT_TYPE;
    public const TESTABLE_DEFAULT_STATUS_CODE = self::DEFAULT_STATUS_CODE;
    public const TESTABLE_DEFAULT_DESCRIPTION = self::DEFAULT_DESCRIPTION;

    public bool $displayErrorDetails = false;
    public bool $logErrors = false;
    public bool $logErrorDetails = false;

    public function setException(\Throwable $exception)
    {
        $this->exception = $exception;
    }
    public function callRespond()
    {
        return $this->respond();
    }
    public function callGetErrorDescription(Throwable $e, ?string $defaultDesc = null)
    {
        return $this->getErrorDescription($e, $defaultDesc);
    }
    public function callGetStatusCode(Throwable $e, ?int $defaultStatusCode = null)
    {
        return $this->getStatusCode($e, $defaultStatusCode);
    }
    public function callDetermineHttpErrorType(Throwable $e, ?HttpErrorTypes $defaultType = null)
    {
        return $this->determineHttpErrorType($e, $defaultType);
    }
    public function callGetErrorType(Throwable $e, ?HttpErrorTypes $defaultType = null)
    {
        return $this->getErrorType($e, $defaultType);
    }
    public function callGetLogLevelAndMessage(Throwable $e, Level $defaultLevel = Level::Error, ?string $defaultMessage = null)
    {
        return $this->getLogLevelAndMessage($e, $defaultLevel, $defaultMessage);
    }
    public function callLogIfEnabled(Level $logLevel, string $logMessage)
    {
        return $this->logIfEnabled($logLevel, $logMessage);
    }
    public function logger(): ?Logger
    {
        return $this->logger;
    }
}

#[CoversClass(HttpErrorHandler::class)]
#[UsesClass(JsonErrorRenderer::class)]
#[UsesClass(ErrorMapper::class)]
#[UsesClass(HttpError::class)]
class HttpErrorHandlerTest extends TestCase
{
    #[Group('constructor')]
    #[DataProvider('dependencyCombinationsProvider')]
    public function testHttpErrorHandlerConstructor(
        callable $params
    ): void {
        [$callableResolver, $responseFactory, $logger, $errorRenderer] = $params($this);

        $errorHandler = new HttpErrorHandler(
            $callableResolver,
            $responseFactory,
            $logger,
            $errorRenderer
        );

        $this->assertSame($callableResolver, TestHelper::getProperty($errorHandler, 'callableResolver'));
        $this->assertSame($responseFactory, TestHelper::getProperty($errorHandler, 'responseFactory'));

        if ($logger !== null) {
            $this->assertSame($logger, TestHelper::getProperty($errorHandler, 'logger'));
        }

        $errorRendererProperty = TestHelper::getProperty($errorHandler, 'errorRenderer');
        $this->assertInstanceOf(ErrorRendererInterface::class, $errorRendererProperty);

        if ($errorRenderer !== null) {
            $this->assertSame($errorRenderer, $errorRendererProperty);
        }
    }

    public static function dependencyCombinationsProvider(): array
    {
        $callableResolver = CallableResolverInterface::class;
        $responseFactory = ResponseFactoryInterface::class;
        $logger = LoggerInterface::class;
        $errorRenderer = ErrorRendererInterface::class;

        return [
            [fn ($context) => self::createMocksFromContext($context, $callableResolver, $responseFactory, $logger, $errorRenderer)],
            [fn ($context) => self::createMocksFromContext($context, $callableResolver, $responseFactory, $logger, null)],
            [fn ($context) => self::createMocksFromContext($context, $callableResolver, $responseFactory, null, $errorRenderer)],
            [fn ($context) => self::createMocksFromContext($context, $callableResolver, $responseFactory, null, null)],
        ];
    }


    public function testRespondGenerateCorrectResponse(): void
    {
        $exception = new \Exception();

        [$callableResolver, $responseFactory, $logger, $errorRenderer] = 
            $this->getDependencyForHttpErrorHandler(true, true);

        $testableHttpErrorHandler = new TestableHttpErrorHandler(
            $callableResolver,
            $responseFactory,
            $logger,
            $errorRenderer
        );

        $statusCode = $testableHttpErrorHandler->callGetStatusCode($exception, $testableHttpErrorHandler::TESTABLE_DEFAULT_STATUS_CODE);
        $type = $testableHttpErrorHandler->callGetErrorType($exception, $testableHttpErrorHandler::TESTABLE_DEFAULT_TYPE);
        $description = $testableHttpErrorHandler->callGetErrorDescription($exception, $testableHttpErrorHandler::TESTABLE_DEFAULT_DESCRIPTION);
        ['level' => $logLevel, 'message' => $logMessage] = $testableHttpErrorHandler->callGetLogLevelAndMessage($exception, Level::Error, $testableHttpErrorHandler::TESTABLE_DEFAULT_DESCRIPTION);

        $error = new HttpError($statusCode, $type, $description);

        $mockResponse = $this->createMock(ResponseInterface::class);
        $errorRenderer->expects($this->once())
            ->method('generateResponseError')
            ->with($error)
            ->willReturn($mockResponse);

        $testableHttpErrorHandler->setException($exception);
        $response = $testableHttpErrorHandler->callRespond();

        $this->assertSame($mockResponse, $response);
    }

    public function testGetStatusCodeWithNotHttpError(): void
    {
        $exceptionStatusCode = -2;
        $exception = new \Exception(code: $exceptionStatusCode);
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->displayErrorDetails = false;

        $expectedStatusCode = $testableHttpErrorHandler::TESTABLE_DEFAULT_STATUS_CODE;

        $statusCode = $testableHttpErrorHandler->callGetStatusCode($exception);
        $this->assertSame($expectedStatusCode, $statusCode);

        $testableHttpErrorHandler->displayErrorDetails = true;

        $statusCode = $testableHttpErrorHandler->callGetStatusCode($exception);
        $this->assertSame($expectedStatusCode, $statusCode);
    }

    public function testGetStatusCodeWithHttpError(): void
    {
        $exceptionStatusCode = -2;
        $exception = $this->createHttpException(code: $exceptionStatusCode);

        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->displayErrorDetails = false;

        $statusCode = $testableHttpErrorHandler->callGetStatusCode($exception);
        $this->assertSame($exceptionStatusCode, $statusCode);

        $testableHttpErrorHandler->displayErrorDetails = true;

        $statusCode = $testableHttpErrorHandler->callGetStatusCode($exception);
        $this->assertSame($exceptionStatusCode, $statusCode);
    }

    public function testDeterminateHttpErrorTypeReturnDefaultWhenItsNotHttpException(): void
    {
        $exception = new \Exception;
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();

        $expected = $testableHttpErrorHandler::TESTABLE_DEFAULT_TYPE;

        $result = $testableHttpErrorHandler->callDetermineHttpErrorType($exception);
        $this->assertSame($expected, $result);
    }

    public function testDeterminateHttpErrorTypeWithHttpError(): void
    {
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $default = $testableHttpErrorHandler::TESTABLE_DEFAULT_TYPE;

        $innerDataProvider = [
            [HttpErrorTypes::RESOURCE_NOT_FOUND, $this->createHttpException(HttpNotFoundException::class)],
            [HttpErrorTypes::NOT_ALLOWED, $this->createHttpException(HttpMethodNotAllowedException::class)],
            [HttpErrorTypes::UNAUTHENTICATED, $this->createHttpException(HttpUnauthorizedException::class)],
            [HttpErrorTypes::FORBIDDEN, $this->createHttpException(HttpForbiddenException::class)],
            [HttpErrorTypes::BAD_REQUEST, $this->createHttpException(HttpBadRequestException::class)],
            [HttpErrorTypes::NOT_IMPLEMENTED, $this->createHttpException(HttpNotImplementedException::class)],
            [$default, $this->createHttpException(code: -999)],
        ];

        foreach ($innerDataProvider as [$expected, $httpException]) {
            $result = $testableHttpErrorHandler->callDetermineHttpErrorType($httpException);
            $this->assertSame($expected, $result);
        }
    }

    public function testGetErrorTypeWithNotHttpError(): void
    {
        $exception = new \Exception;
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();

        $expected = $testableHttpErrorHandler::TESTABLE_DEFAULT_TYPE;

        $result = $testableHttpErrorHandler->callGetErrorType($exception);
        $this->assertSame($expected, $result);
    }

    public function testGetErrorTypeWithHttpError(): void
    {
        $exception = $this->createHttpException(HttpForbiddenException::class);

        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();

        $result = $testableHttpErrorHandler->callGetErrorType($exception);
        $this->assertSame(HttpErrorTypes::FORBIDDEN, $result);
    }

    public function testGetErrorDescriptionWithNotHttpError(): void
    {
        $exceptionMessage = 'custom-message';
        $exception = new \Exception($exceptionMessage);
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->displayErrorDetails = false;

        $expectedDescription = $testableHttpErrorHandler::TESTABLE_DEFAULT_DESCRIPTION;

        $description = $testableHttpErrorHandler->callGetErrorDescription($exception);
        $this->assertSame($expectedDescription, $description);

        $testableHttpErrorHandler->displayErrorDetails = true;

        $description = $testableHttpErrorHandler->callGetErrorDescription($exception);
        $this->assertSame($exceptionMessage, $description);
    }

    public function testGetErrorDescriptionWithHttpError(): void
    {
        $message = 'Custom-Http-Error-Message';
        $exception = $this->createHttpException(message: $message);

        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->displayErrorDetails = false;

        $description = $testableHttpErrorHandler->callGetErrorDescription($exception);
        $this->assertSame($message, $description);

        $testableHttpErrorHandler->displayErrorDetails = true;

        $description = $testableHttpErrorHandler->callGetErrorDescription($exception);
        $this->assertSame($message, $description);
    }

    public function testGetLogLevelAndMessageWithNotHttpError(): void
    {
        $exceptionMessage = 'custom-message';
        $exceptionCode = E_WARNING;
        $exception = new \Exception(message: $exceptionMessage, code: $exceptionCode);
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->logErrorDetails = false;
        
        $defaultLevel = Level::Error;
        $defaultMessage = $testableHttpErrorHandler::TESTABLE_DEFAULT_DESCRIPTION;

        ['level' => $resultLevel, 'message' => $resultMessage] = $testableHttpErrorHandler->callGetLogLevelAndMessage($exception, $defaultLevel);
        $this->assertSame(ErrorMapper::map($exceptionCode), $resultLevel);
        $this->assertSame($defaultMessage, $resultMessage);
        
        $testableHttpErrorHandler->logErrorDetails = true;

        ['level' => $resultLevel, 'message' => $resultMessage] = $testableHttpErrorHandler->callGetLogLevelAndMessage($exception, $defaultLevel);
        $this->assertSame(ErrorMapper::map($exceptionCode), $resultLevel);
        $this->assertSame($exceptionMessage, $resultMessage);
    }
    
    public function testGetLogLevelAndMessageWithHttpError(): void
    {
        $exceptionMessage = 'Custom-Http-Error-Message';
        $exceptionCode = E_WARNING;
        $exception = $this->createHttpException(message: $exceptionMessage, code: $exceptionCode);
        $testableHttpErrorHandler = $this->getTestableHttpErrorHandlerInstance();
        $testableHttpErrorHandler->logErrorDetails = false;
        
        $defaultLevel = Level::Error;
        $defaultMessage = $testableHttpErrorHandler::TESTABLE_DEFAULT_DESCRIPTION;

        ['level' => $resultLevel, 'message' => $resultMessage] = $testableHttpErrorHandler->callGetLogLevelAndMessage($exception, $defaultLevel);
        $this->assertSame($defaultLevel, $resultLevel);
        $this->assertSame($defaultMessage, $resultMessage);
        
        $testableHttpErrorHandler->logErrorDetails = true;

        ['level' => $resultLevel, 'message' => $resultMessage] = $testableHttpErrorHandler->callGetLogLevelAndMessage($exception, $defaultLevel);
        $this->assertSame($defaultLevel, $resultLevel);
        $this->assertSame($exceptionMessage, $resultMessage);
    }

    public function testLogIfEnabledDoesNotLogWhenLogErrorsFalse(): void
    {
        $mockLogger = $this->createMock(Logger::class);
        $mockLogger->expects($this->never())->method('log');

        [$callableResolver, $responseFactory,,] = $this->getDependencyForHttpErrorHandler();

        $testableHttpErrorHandler = new TestableHttpErrorHandler($callableResolver, $responseFactory, $mockLogger);
        $testableHttpErrorHandler->logErrors = false;

        $testableHttpErrorHandler->callLogIfEnabled(Level::Emergency, 'Test emergency message');
    }

    public function testLogIfEnabledLogsWhenLogErrorsTrue(): void
    {
        $mockLogger = $this->createMock(Logger::class);
        $mockLogger->expects($this->once())->method('log');

        [$callableResolver, $responseFactory,,] = $this->getDependencyForHttpErrorHandler();

        $testableHttpErrorHandler = new TestableHttpErrorHandler($callableResolver, $responseFactory, $mockLogger);
        $testableHttpErrorHandler->logErrors = true;

        $testableHttpErrorHandler->callLogIfEnabled(Level::Emergency, 'Test emergency message');
    }

    private function createHttpException(?string $httpExceptionClass = null, string $message = '', int $code = 0, ?\Throwable $prev = null): HttpException
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        
        if ($httpExceptionClass === null || ! class_exists($httpExceptionClass) || ! is_subclass_of($httpExceptionClass, HttpException::class))
        {
            return new HttpException($requestMock, $message, $code, $prev);
        } 
        else if (class_exists($httpExceptionClass) && is_subclass_of($httpExceptionClass, HttpSpecializedException::class))
        {
            return new $httpExceptionClass($requestMock, $message, $prev);
        }

        throw new \RuntimeException('Invalid class');
    }

    private function getTestableHttpErrorHandlerInstance(bool $withLogger = false, bool $withErrorRenderer = false): TestableHttpErrorHandler
    {
        return new TestableHttpErrorHandler(...$this->getDependencyForHttpErrorHandler($withLogger, $withErrorRenderer));
    }

    private function getDependencyForHttpErrorHandler(bool $withLogger = false, bool $withErrorRenderer = false): array
    {
        $callableResolver = CallableResolverInterface::class;
        $responseFactory = ResponseFactoryInterface::class;
        $logger = $withLogger ? LoggerInterface::class : null;
        $errorRenderer = $withErrorRenderer ? ErrorRendererInterface::class : null;

        return self::createMocksFromContext($this, $callableResolver, $responseFactory, $logger, $errorRenderer);
    }

    private static function createMockFromContext($context, string $class): MockObject
    {
        return $context->createMock($class);
    }

    private static function createMocksFromContext($context, ?string ...$classes): array
    {
        return array_map(fn ($class) => $class ? self::createMockFromContext($context, $class) : null, $classes);
    }

    private function getProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}