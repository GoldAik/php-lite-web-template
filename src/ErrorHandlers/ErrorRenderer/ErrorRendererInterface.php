<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\ErrorRenderer;

use App\ErrorHandlers\DTO\HttpError;
use Psr\Http\Message\ResponseInterface;

interface ErrorRendererInterface 
{
    public function generateResponseError(HttpError $error): ResponseInterface;
}