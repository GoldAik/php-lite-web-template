<?php

declare(strict_types = 1);

namespace Test\Unit\ErrorHandlers\DTO;

use PHPUnit\Framework\TestCase;
use App\ErrorHandlers\DTO\HttpError;
use App\ErrorHandlers\Enums\HttpErrorTypes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversClass(HttpError::class)]
class HttpErrorTest extends TestCase
{
    #[Group('constructor')]
    #[TestWith([400, HttpErrorTypes::BAD_REQUEST, 'Bad request error'])]
    #[TestWith([404, HttpErrorTypes::RESOURCE_NOT_FOUND, 'Resource not found'])]
    #[TestWith([500, HttpErrorTypes::SERVER_ERROR, 'Server error'])]
    public function testHttpErrorConstruction(int $statusCode, HttpErrorTypes $type, string $description): void
    {
        $error = new HttpError($statusCode, $type, $description);

        $this->assertSame($statusCode, $error->statusCode);
        $this->assertSame($type, $error->type);
        $this->assertSame($description, $error->description);
    }

    #[Group('invalid-types')]
    #[TestWith(['400', HttpErrorTypes::BAD_REQUEST, 'Bad request description'])]
    #[TestWith([400, 'Bad Request', 'Bad request description'])]
    #[TestWith([400, HttpErrorTypes::BAD_REQUEST, 2])]
    public function testInvalidTypeThrowsTypeError($statusCode, $type, $description)
    {
        $this->expectException(\TypeError::class);
        new HttpError($statusCode, $type, $description);
    }
}