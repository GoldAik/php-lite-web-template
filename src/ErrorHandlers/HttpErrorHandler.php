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
        $statusCode = self::DEFAULT_STATUS_CODE;
        $type = self::DEFAULT_TYPE;
        $description = self::DEFAULT_DESCRIPTION;

        $logLevel = Level::Error;
        $logMessage = self::DEFAULT_DESCRIPTION;
        if ($this->logErrorDetails) {
            $logMessage = $exception->getMessage();
        }

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            $type = match (true) {
                $exception instanceof HttpNotFoundException => HttpErrorTypes::RESOURCE_NOT_FOUND,
                $exception instanceof HttpMethodNotAllowedException => HttpErrorTypes::NOT_ALLOWED,
                $exception instanceof HttpUnauthorizedException => HttpErrorTypes::UNAUTHENTICATED,
                $exception instanceof HttpForbiddenException => HttpErrorTypes::FORBIDDEN,
                $exception instanceof HttpBadRequestException => HttpErrorTypes::BAD_REQUEST,
                $exception instanceof HttpNotImplementedException => HttpErrorTypes::NOT_IMPLEMENTED,
                default => $type,
            };
            
        } elseif ($exception instanceof Exception || $exception instanceof Throwable) {
            $logLevel = ErrorMapper::map($exception->getCode()); 

            if ($this->displayErrorDetails) {
                $description = $exception->getMessage();
            }
        } 

        if ($this->logErrors) {
            $this->logger()?->log($logLevel, $logMessage);
        }

        $error = new HttpError($statusCode, $type, $description);

        $response = $this->errorRenderer->generateResponseError($error);
        return $response;
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