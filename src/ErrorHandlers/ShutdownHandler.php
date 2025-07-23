<?php

declare(strict_types = 1);

namespace App\ErrorHandlers;

use App\ErrorHandlers\Logger\ErrorMapper;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;

/**
 * Class ShutdownHandler
 *
 * Handles errors and shutdown procedures, providing options for logging,
 * displaying error details, and controlling which errors are ignored.
 *
 * Uses flags to configure behavior such as showing detailed errors,
 * logging errors, and ignoring specific error types.
 */
class ShutdownHandler
{
    /**
     * Flag to display error details on the page in debug mode.
     */
    public const DISPLAY_ERROR_DETAILS = 1 << 0;

    /**
     * Flag to log errors using the logger.
     */
    public const LOG_ERRORS = 1 << 1;

    /**
     * Flag to log detailed error information.
     */
    public const LOG_ERROR_DETAILS = 1 << 2;

    /**
     * Flag to ignore errors when displaying error details.
     */
    public const IGNORE_ERRORS_ON_DISPLAY_DETAILS = 1 << 3;

    /**
     * @var bool Whether to display error details to the user.
     */
    private bool $displayErrorDetails;

    /**
     * @var bool Whether errors should be logged.
     */
    private bool $logErrors;

    /**
     * @var bool Whether to log error details.
     */
    private bool $logErrorDetails;

    /**
     * @var bool Whether to ignore errors during display of error details.
     */
    private bool $ignoreErrorsOnDisplayDetails;

    /**
     * Constructor.
     *
     * @param Request $request The request object.
     * @param HttpErrorHandler $errorHandler The error handler.
     * @param ?Logger $logger Optional logger for error logging.
     * @param int $ignoreErrors Error types to ignore (e.g., E_NOTICE | E_WARNING).
     * @param int $flags Configuration flags controlling behavior.
     */
    public function __construct(
        private Request $request,
        private HttpErrorHandler $errorHandler,
        private ?Logger $logger,
        private int $ignoreErrors = E_NOTICE | E_WARNING,
        private int $flags = 0,
    ) {
        $this->ignoreErrorsOnDisplayDetails = ($this->flags & self::IGNORE_ERRORS_ON_DISPLAY_DETAILS) === self::IGNORE_ERRORS_ON_DISPLAY_DETAILS;
        $this->logErrors = ($this->flags & self::LOG_ERRORS) === self::LOG_ERRORS;
        $this->logErrorDetails = ($this->flags & self::LOG_ERROR_DETAILS) === self::LOG_ERROR_DETAILS;
    }

    /**
     * Factory method to create an instance based on debug mode.
     *
     * @param Request $request The request object.
     * @param HttpErrorHandler $errorHandler The error handler.
     * @param ?Logger $logger Optional logger.
     * @param bool $debugMode Whether to enable debug mode.
     * @return self
     */
    public static function make(
        Request $request,
        HttpErrorHandler $errorHandler,
        ?Logger $logger,
        bool $debugMode = false,
    ): self {
        $flags = 0;

        if ($debugMode) {
            $flags |= self::DISPLAY_ERROR_DETAILS;
        } else {
            $flags |= self::LOG_ERRORS;
            $flags |= self::LOG_ERROR_DETAILS;
        }

        return new self($request, $errorHandler, $logger, flags: $flags);
    }

    /**
     * Invokes the error handling logic during shutdown.
     */
    public function __invoke()
    {        
        $error = error_get_last();
        if (! $error) {
            return;
        }
        
        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorMessage = $error['message'];
        $errorType = $error['type'];
        $message = 'An error while processing your request. Please try again later.';

        if ($this->logErrors) {
            $level = ErrorMapper::map($errorType);

            $logMessage = $level->getName() . ': ' . $message;

            if ($this->logErrorDetails) {
                $logMessage = $level->getName() . ": {$errorMessage}. on line {$errorLine} in file {$errorFile}";
            }

            $this->logger()?->log($level, $logMessage);
        }

        if ((! $this->displayErrorDetails || $this->ignoreErrorsOnDisplayDetails)
                && ($errorType & $this->ignoreErrors) === $errorType) {
            return;
        }

        if ($this->displayErrorDetails) {
            switch ($errorType) {
                case E_USER_ERROR:
                    $message = "FATAL ERROR: {$errorMessage}. ";
                    $message .= " on line {$errorLine} in file {$errorFile}.";
                    break;

                case E_USER_WARNING:
                    $message = "WARNING: {$errorMessage}";
                    break;

                case E_USER_NOTICE:
                    $message = "NOTICE: {$errorMessage}";
                    break;

                default:
                    $message = "ERROR: {$errorMessage}";
                    $message .= " on line {$errorLine} in file {$errorFile}.";
                    break;
            }
        }

        $exception = new HttpInternalServerErrorException($this->request, $message);
        $response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, false, false);
        
        if (ob_get_length()) {
            ob_clean();
        }

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

    /**
     * Gets the logger instance.
     *
     * @return ?Logger The logger or null if not set.
     */
    private function logger(): ?Logger
    {
        return $this->logger;
    }

    /**
     * Gets the current flags.
     *
     * @return int The flags.
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * Sets the configuration flags.
     *
     * @param int $flags The new flags.
     * @return self
     */
    public function setFlags(int $flags): self
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * Gets the error types to ignore.
     *
     * @return int The error types.
     */
    public function getIgnoreErrors(): int
    {
        return $this->ignoreErrors;
    }

    /**
     * Sets the error types to ignore.
     *
     * @param int $ignoreErrors The error types.
     * @return self
     */
    public function setIgnoreErrors(int $ignoreErrors): self
    {
        $this->ignoreErrors = $ignoreErrors;
        return $this;
    }
}