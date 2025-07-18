<?php

declare(strict_types = 1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TokenMiddleware
{
    private const TOKEN = 'abc';

    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $token = $request->getQueryParams()['token'] ?? null;

        if (! $token || $this::TOKEN !== $token) {
            $response = $this->responseFactory->createResponse(401);
            $response->getBody()->write('incorrect token');

            return $response;
        }

        return $requestHandler->handle($request);
    }
}