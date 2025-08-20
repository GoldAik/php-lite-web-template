<?php

declare(strict_types = 1);

namespace App\Session;

/**
 * Interface SessionInterface
 * 
 * Defines the contract for session management classes.
 */
interface SessionInterface
{
    /**
     * Starts a new session or resumes the current one.
     * 
     * @return void
     */
    public function start(): void;

    /**
     * Saves the current session data.
     * 
     * @return void
     */
    public function save(): void;

    /**
     * Regenerates the session ID to prevent fixation attacks.
     * 
     * @return bool True if the session ID was regenerated successfully, false otherwise.
     */
    public function regenerate(): bool;

    /**
     * Retrieves a value from the session.
     *
     * @param string $key The key of the session variable.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value of the session variable or the default.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Sets a value in the session.
     *
     * @param string $key The key of the session variable.
     * @param mixed $value The value to set.
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Unsets a session variable.
     *
     * @param string $key The key of the session variable to remove.
     * @return void
     */
    public function unset(string $key): void;
}