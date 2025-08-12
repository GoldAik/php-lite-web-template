<?php

declare(strict_types = 1);

namespace Test\Unit\ErrorHandler\ErrorRenderer;

use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\Enums\HttpErrorTypes;
use App\ErrorHandlers\ErrorRenderer\JsonErrorRenderer;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(JsonErrorRenderer::class)]
#[UsesClass(HttpError::class)]
class JsonErrorRendererTest extends TestCase
{
    #[Group('constructor')]
    public function testJsonErrorRendererConstruction(): void
    {
        $responseFactoryMock = $this->createMock(ResponseFactoryInterface::class);
        
        $renderer = new JsonErrorRenderer($responseFactoryMock);
        
        $reflection = new \ReflectionClass($renderer);
        $property = $reflection->getProperty('responseFactory');
        $property->setAccessible(true);
        $retrievedFactory = $property->getValue($renderer);
        
        $this->assertSame($responseFactoryMock, $retrievedFactory);
    }

    #[TestWith([400, HttpErrorTypes::BAD_REQUEST, 'Bad request'])]
    #[TestWith([401, HttpErrorTypes::UNAUTHENTICATED, 'Unauthorized access'])]
    #[TestWith([403, HttpErrorTypes::FORBIDDEN, 'Access forbidden'])]
    #[TestWith([404, HttpErrorTypes::RESOURCE_NOT_FOUND, 'Resource not found'])]
    #[TestWith([500, HttpErrorTypes::SERVER_ERROR, 'Internal server error'])]
    public function testGenerateResponseErrorReturnsProperResponse(
        int $statusCode, HttpErrorTypes $errorType, string $errorDescription
    ): void
    {
        $responseFactoryMock = $this->createMock(ResponseFactoryInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $expectedPayload = json_encode([
            'statusCode' => $statusCode,
            'type' => $errorType,
            'description' => $errorDescription
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $responseFactoryMock->expects($this->once())
            ->method('createResponse')
            ->with($statusCode)
            ->willReturn($responseMock);
        
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($responseMock);
        
        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($expectedPayload);
        
        $error = new HttpError(
            $statusCode,
            $errorType,
            $errorDescription
        );
        
        $renderer = new JsonErrorRenderer($responseFactoryMock);
        $response = $renderer->generateResponseError($error);
        
        $this->assertSame($responseMock, $response);
    }
}