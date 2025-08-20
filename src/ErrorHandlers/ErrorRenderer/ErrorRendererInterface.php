<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\ErrorRenderer;

use App\ErrorHandlers\DTO\HttpError;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ErrorRendererInterface
 * 
 * Defines the contract for rendering error responses.
 */
interface ErrorRendererInterface
{
    /**
     * Generates a PSR-7 Response containing the error details in the appropriate format.
     * 
     * @param HttpError $error The error object with details to be rendered.
     * @return ResponseInterface The PSR-7 response with the rendered error.
     */
    public function generateResponseError(HttpError $error): ResponseInterface;
}