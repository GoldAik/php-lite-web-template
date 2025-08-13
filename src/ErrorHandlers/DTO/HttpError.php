<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\DTO;

use App\ErrorHandlers\Enums\HttpErrorTypes;

/**
 * Class HttpError
 * 
 * Represents an HTTP error with status code, type, and description.
 */
class HttpError
{
    /**
     * Creates an instance of HttpError.
     *
     * @param int $statusCode The HTTP status code.
     * @param HttpErrorTypes $type The type of HTTP error.
     * @param string $description A description of the error.
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly HttpErrorTypes $type,
        public readonly string $description,
    ) {}
}