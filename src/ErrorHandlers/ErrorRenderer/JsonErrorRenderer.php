<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\ErrorRenderer;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\ErrorRenderer\ErrorRendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class JsonErrorRenderer
 * 
 * Responsible for generating JSON formatted error responses.
 * Implements ErrorRendererInterface.
 * 
 * @package App\ErrorHandlers\ErrorRenderer
 */
class JsonErrorRenderer implements ErrorRendererInterface
{
    /**
     * Response factory used to create HTTP responses.
     * 
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * Constructor.
     * 
     * @param ResponseFactoryInterface $responseFactory Factory to create responses.
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Generates a JSON response containing the error details.
     * 
     * @param HttpError $error The error object with details to serialize.
     * @return ResponseInterface The PSR-7 response with JSON payload.
     */
    public function generateResponseError(HttpError $error): ResponseInterface
    {
        $payload = json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        $response = $this->responseFactory->createResponse($error->statusCode);
        $response = $response->withHeader('Content-Type', 'application/json');       
        $response->getBody()->write($payload);

        return $response;
    }
}