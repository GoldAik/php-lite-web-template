<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\DTO;

use App\ErrorHandlers\Enums\HttpErrorTypes;

class HttpError
{
    public function __construct(
        public readonly int $statusCode,
        public readonly HttpErrorTypes $type,
        public readonly string $description,
    ) {}
}