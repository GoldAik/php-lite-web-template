<?php

declare(strict_types = 1);

namespace Test\Unit\ErrorHandler\Logger;

use PHPUnit\Framework\TestCase;
use App\ErrorHandlers\Logger\ErrorMapper;
use Monolog\Level;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversClass(ErrorMapper::class)]
class ErrorMapperTest extends TestCase
{
    #[Group('mapping')]
    #[TestWith([E_NOTICE])]
    #[TestWith([E_USER_NOTICE])]
    public function testMapWellMappingToNotice(int $errorType)
    {
        $level = ErrorMapper::map($errorType);
        $this->assertSame(Level::Notice, $level, "Failed mapping for error type: {$errorType}");
    }

    #[Group('mapping')]
    #[TestWith([E_WARNING])]
    #[TestWith([E_CORE_WARNING])]
    #[TestWith([E_USER_WARNING])]
    #[TestWith([E_COMPILE_WARNING])]
    #[TestWith([E_DEPRECATED])]
    #[TestWith([E_USER_DEPRECATED])]
    public function testMapWellMappingToWarning(int $errorType)
    {
        $level = ErrorMapper::map($errorType);
        $this->assertSame(Level::Warning, $level, "Failed mapping for error type: {$errorType}");
    }

    #[Group('mapping')]
    #[TestWith([E_ERROR])]
    #[TestWith([E_CORE_ERROR])]
    #[TestWith([E_USER_ERROR])]
    #[TestWith([E_COMPILE_ERROR])]
    #[TestWith([E_PARSE])]
    #[TestWith([E_RECOVERABLE_ERROR])]
    public function testMapWellMappingToError(int $errorType)
    {
        $level = ErrorMapper::map($errorType);
        $this->assertSame(Level::Error, $level, "Failed mapping for error type: {$errorType}");
    }

    #[Group('edge-cases')]
    #[TestWith([99999])]
    #[TestWith([-1])]
    #[TestWith([0])]
    public function testMapUnknownErrorTypeDefaultsToError(int $unknownErrorType)
    {
        $result = ErrorMapper::map($unknownErrorType);
        $this->assertSame(Level::Error, $result);
    }

    #[Group('invalid-types')]
    #[TestWith(['1'])]
    #[TestWith(['true'])]
    #[TestWith([true])]
    #[TestWith([null])]
    #[TestWith([new \stdClass()])]
    public function testMapInvalidTypeThrowsTypeError($incorrectTypeError)
    {
        $this->expectException(\TypeError::class);
        ErrorMapper::map($incorrectTypeError);
    }
}