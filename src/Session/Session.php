<?php

declare(strict_types = 1);

namespace App\Session;

use App\Session\DTO\Options;
use App\Session\Global\SessionGlobalFunctions;
use App\Session\Global\SessionGlobalFunctionsInterface;

/**
 * Class Session
 * 
 * Implements session management with dependency injection for session functions.
 * 
 * @package App\Session
 */
class Session implements SessionInterface
{
    /**
     * Constructor
     * 
     * @param Options $options Configuration options for the session.
     * @param SessionGlobalFunctionsInterface $functions Optional. Session functions wrapper. Defaults to a new SessionGlobalFunctions instance.
     */
    public function __construct(
        protected readonly Options $options,
        protected readonly SessionGlobalFunctionsInterface $functions = new SessionGlobalFunctions(),
    ) { }

    /**
     * Starts a new session.
     * 
     * @throws \RuntimeException If session has already been started or headers are already sent.
     */
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

    /**
     * Closes the session and writes session data.
     */
    public function save(): void
    {
        $this->functions->sessionWriteClose();
    }

    /**
     * Regenerates the session ID.
     *
     * @param bool $deleteOld Whether to delete the old session data.
     * @return bool True on success, false on failure.
     */
    public function regenerate(bool $deleteOld = false): bool
    {
        return $this->functions->sessionRegenerateId($deleteOld);
    }

    /**
     * Retrieves a value from the session.
     * 
     * @param string $key The session key.
     * @param mixed|null $default Default value if key does not exist.
     * @return mixed The value associated with the key or default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    /**
     * Sets a session value.
     * 
     * @param string $key The session key.
     * @param mixed $value The value to set.
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Removes a key from the session.
     * 
     * @param string $key The session key to remove.
     */
    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }
}