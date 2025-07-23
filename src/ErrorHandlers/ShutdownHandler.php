<?php

declare(strict_types = 1);

namespace App\ErrorHandlers;

use App\ErrorHandlers\Logger\ErrorMapper;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;

class ShutdownHandler
{
    /**
     * ShutdownHandler constructor.
     */
    public function __construct(
        private Request $request,
        private HttpErrorHandler $errorHandler,
        private bool $displayErrorDetails,
        private ?Logger $logger,
        private int $ignoreErrors = E_NOTICE | E_WARNING,
        private bool $ignoreErrorsOnDisplayDetails = false,
    ) { }

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

        $level = ErrorMapper::map($errorType);
        $logMessage = $level->getName() . ": {$errorMessage}. on line {$errorLine} in file {$errorFile}";
        $this->logger()?->log($level, $logMessage);

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
        $response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, true, true);
        
        if (ob_get_length()) {
            ob_clean();
        }

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

    private function logger(): ?Logger
    {
        return $this->logger;
    }
}