<?php

declare(strict_types = 1);

namespace App\Session;

use App\Session\DTO\Options;
use App\Session\Global\SessionGlobalFunctions;
use App\Session\Global\SessionGlobalFunctionsInterface;

class Session implements SessionInterface
{
    public function __construct(
        protected readonly Options $options,
        protected readonly SessionGlobalFunctionsInterface $functions = new SessionGlobalFunctions(),
    ) { }

    public function start(): void
    {
        if ($this->functions->sessionStatus() === PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Session has already been started');
        }

        if ($this->functions->headersSent($filename, $line)) {
            throw new \RuntimeException('Headers already sent');
        }

        $this->functions->sessionSetCookieParams([
            'lifetime' => $this->options->lifetime,
            'path'     => $this->options->path,
            'secure'   => $this->options->secure,
            'httponly' => $this->options->httpOnly,
            'samesite' => $this->options->sameSite->value,
        ]);

        $this->functions->sessionSavePath($this->options->storagePath);

        $this->functions->sessionStart();
    }

    public function save(): void
    {
        $this->functions->sessionWriteClose();
    }

    public function regenerate(bool $deleteOld = false): bool
    {
        return $this->functions->sessionRegenerateId($deleteOld);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }
}