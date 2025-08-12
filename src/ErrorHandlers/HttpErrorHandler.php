<?php

declare(strict_types = 1);

namespace App\ErrorHandlers;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\Enums\HttpErrorTypes;
use App\ErrorHandlers\ErrorRenderer\ErrorRendererInterface;
use App\ErrorHandlers\ErrorRenderer\JsonErrorRenderer;
use App\ErrorHandlers\Logger\ErrorMapper;
use Monolog\Level;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;
use Exception;

class HttpErrorHandler extends ErrorHandler
{
    protected const DEFAULT_TYPE = HttpErrorTypes::SERVER_ERROR;
    protected const DEFAULT_STATUS_CODE = 500;
    protected const DEFAULT_DESCRIPTION = 'An internal error has occurred while processing your request.';
    
    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null,
        protected ?ErrorRendererInterface $errorRenderer = null,
    ) {
        parent::__construct($callableResolver, $responseFactory, $logger);
        $this->errorRenderer = $errorRenderer ?? $this->getDefaultRenderer();
    }

    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;

        $statusCode = $this->getStatusCode($exception, self::DEFAULT_STATUS_CODE);
        $type = $this->getErrorType($exception, self::DEFAULT_TYPE);
        $description = $this->getErrorDescription($exception, self::DEFAULT_DESCRIPTION);

        ['level' => $logLevel, 'message' => $logMessage] = $this->getLogLevelAndMessage($exception, Level::Error, self::DEFAULT_DESCRIPTION);

        $this->logIfEnabled($logLevel, $logMessage);

        $error = new HttpError($statusCode, $type, $description);

        $response = $this->errorRenderer->generateResponseError($error);
        return $response;
    }

    protected function getStatusCode(Throwable $exception, ?int $defaultStatusCode = null): int
    {
        $defaultStatusCode ??= self::DEFAULT_STATUS_CODE;

        if ($exception instanceof HttpException) {
            return $exception->getCode();
        }

        return $defaultStatusCode;
    }

    protected function determineHttpErrorType(Throwable $exception, ?HttpErrorTypes $defaultErrorType = null): HttpErrorTypes
    {
        $defaultErrorType ??= self::DEFAULT_TYPE;

        if ($exception instanceof HttpException) {
            return match (true) {
                $exception instanceof HttpNotFoundException => HttpErrorTypes::RESOURCE_NOT_FOUND,
                $exception instanceof HttpMethodNotAllowedException => HttpErrorTypes::NOT_ALLOWED,
                $exception instanceof HttpUnauthorizedException => HttpErrorTypes::UNAUTHENTICATED,
                $exception instanceof HttpForbiddenException => HttpErrorTypes::FORBIDDEN,
                $exception instanceof HttpBadRequestException => HttpErrorTypes::BAD_REQUEST,
                $exception instanceof HttpNotImplementedException => HttpErrorTypes::NOT_IMPLEMENTED,
                default => $defaultErrorType,
            };
        }
        return $defaultErrorType;
    }

    protected function getErrorType(Throwable $exception, ?HttpErrorTypes $defaultErrorType = null): HttpErrorTypes
    {
        $defaultErrorType ??= self::DEFAULT_TYPE;

        if ($exception instanceof HttpException) {
            return $this->determineHttpErrorType($exception, $defaultErrorType);
        }
        return $defaultErrorType;
    }

    protected function getErrorDescription(Throwable $exception, ?string $defaultDescription = null): string
    {
        $defaultDescription ??= self::DEFAULT_DESCRIPTION;

        if ($this->displayErrorDetails || $exception instanceof HttpException) {
            return $exception->getMessage();
        }
        return $defaultDescription;
    }

    protected function getLogLevelAndMessage(Throwable $exception, Level $defaultLevel = Level::Error, ?string $defaultMessage = null): array
    {
        $level = $defaultLevel;
        $message = $defaultMessage ?? self::DEFAULT_DESCRIPTION;

        if ($this->logErrorDetails) {
            $message = $exception->getMessage();
        }

        if (! $exception instanceof HttpException && ($exception instanceof Exception || $exception instanceof Throwable)) {
            $level = ErrorMapper::map($exception->getCode());
        }

        return ['level' => $level, 'message' => $message];
    }

    protected function logIfEnabled(Level $logLevel, string $logMessage): void
    {
        if ($this->logErrors) {
            $this->logger()?->log($logLevel, $logMessage);
        }
    }

    protected function getDefaultRenderer(): ErrorRendererInterface
    {
        $responseFactory = $this->responseFactory;
        return new JsonErrorRenderer($responseFactory);
    }

    private function logger(): ?Logger
    {
        return $this->logger;
    }
}