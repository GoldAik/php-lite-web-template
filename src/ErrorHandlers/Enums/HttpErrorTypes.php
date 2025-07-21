<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\Enums;

enum HttpErrorTypes: string
{
    case BAD_REQUEST = 'BAD_REQUEST';
    case INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    case NOT_ALLOWED = 'NOT_ALLOWED';
    case NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    case SERVER_ERROR = 'SERVER_ERROR';
    case UNAUTHENTICATED = 'UNAUTHENTICATED';
    case FORBIDDEN = 'FORBIDDEN';
}