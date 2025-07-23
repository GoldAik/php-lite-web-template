<?php

declare(strict_types = 1);

namespace App\ErrorHandlers\Logger;

use Monolog\Level;

class ErrorMapper
{
    /**
     * Mapping of PHP error constants to Monolog log levels.
     *
     * This array defines how PHP's error severity levels are mapped 
     * to Monolog's log levels. It ensures that PHP errors are logged with appropriate severity.
     *
     * Keys are PHP error constants, and values are corresponding Monolog Level objects.
     *
     * @phpstan-var array<int, Level> Mapping of PHP error constants to Monolog log levels.
     */
    protected const PHP_ERRORS_2_LEVELS = [
        E_NOTICE => Level::Notice,
        E_USER_NOTICE => Level::Notice,

        E_WARNING => Level::Warning,
        E_CORE_WARNING => Level::Warning,
        E_USER_WARNING => Level::Warning,
        E_COMPILE_WARNING => Level::Warning,
        E_DEPRECATED => Level::Warning,
        E_USER_DEPRECATED => Level::Warning,

        E_ERROR => Level::Error,
        E_CORE_ERROR => Level::Error,
        E_USER_ERROR => Level::Error,
        E_COMPILE_ERROR => Level::Error,
        E_PARSE => Level::Error,
        E_RECOVERABLE_ERROR => Level::Error,
    ];

    /**
     * Maps a PHP error type to the corresponding Monolog log level.
     *
     * This method takes an integer representing a PHP error type (e.g., E_WARNING, E_NOTICE)
     * and returns the appropriate Monolog Level object based on the predefined mapping.
     * If the error type is not found in the mapping, it defaults to Level::Error.
     *
     * @param int $errorType The PHP error constant to map.
     * @return Level The corresponding Monolog log level.
     */
    public static function map(int $errorType): Level
    {
        return self::PHP_ERRORS_2_LEVELS[$errorType] ?? Level::Error;
    }
}