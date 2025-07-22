<?php

declare(strict_types = 1);

namespace App\ErrorHandlers;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\Enums\HttpErrorTypes;
use App\ErrorHandlers\ErrorRenderer\ErrorRendererInterface;
use App\ErrorHandlers\ErrorRenderer\JsonErrorRenderer;
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
use Exception;
use Throwable;

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

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            if ($exception instanceof HttpNotFoundException) {
                $type = HttpErrorTypes::RESOURCE_NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $type = HttpErrorTypes::NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $type = HttpErrorTypes::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $type = HttpErrorTypes::FORBIDDEN;
            } elseif ($exception instanceof HttpBadRequestException) {
                $type = HttpErrorTypes::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $type = HttpErrorTypes::NOT_IMPLEMENTED;
            }
        }

        if (
            !($exception instanceof HttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        if ($this->logger) {
            $this->logger->error($description);
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
}