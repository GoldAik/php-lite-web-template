<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\ErrorRenderer;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\ErrorRenderer\ErrorRendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class JsonErrorRenderer implements ErrorRendererInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function generateResponseError(HttpError $error): ResponseInterface
    {
        $payload = json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        $response = $this->responseFactory->createResponse($error->statusCode);
        $response = $response->withHeader('Content-Type', 'application/json');       
        $response->getBody()->write($payload);

        return $response;
    }
}