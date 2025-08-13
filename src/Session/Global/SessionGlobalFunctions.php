<?php

declare(strict_types = 1);

namespace App\Session\Global;

/**
 * Class SessionGlobalFunctions
 * 
 * Provides wrapper methods for native PHP session functions.
 * Implements the SessionGlobalFunctionsInterface.
 * 
 * @package App\Session\Global
 */
class SessionGlobalFunctions implements SessionGlobalFunctionsInterface
{
    /**
     * Gets the current session status.
     *
     * @return int One of the session status constants:
     *             PHP_SESSION_DISABLED, PHP_SESSION_NONE, or PHP_SESSION_ACTIVE.
     */
    public function sessionStatus(): int
    {
        return session_status();
    }

    /**
     * Checks if headers have already been sent.
     *
     * @param string|null $filename Reference variable that will be set to the filename of the output (if any).
     * @param int|null $line Reference variable that will be set to the line number where output started.
     * @return bool True if headers have already been sent, false otherwise.
     */
    public function headersSent(?string &$filename = null, ?int &$line = null): bool
    {
        return headers_sent($filename, $line);
    }

    /**
     * Sets the session cookie parameters.
     *
     * @param array $params Array of parameters, such as 'lifetime', 'path', 'domain', 'secure', 'httponly', etc.
     * @return bool True on success, false on failure.
     */
    public function sessionSetCookieParams(array $params): bool
    {
        return session_set_cookie_params($params);
    }

    /**
     * Gets or sets the session save path.
     *
     * @param string|null $path Path where session data files are stored. If null, returns current path.
     * @return string|false Returns the current session save path, or false on failure.
     */
    public function sessionSavePath(null|string $path): string|false
    {
        return session_save_path($path);
    }

    /**
     * Starts a new session or resumes the current one.
     *
     * @return bool True on success or false on failure.
     */
    public function sessionStart(): bool
    {
        return session_start();
    }

    /**
     * Writes session data and ends the session.
     *
     * @return bool True on success or false on failure.
     */
    public function sessionWriteClose(): bool
    {
        return session_write_close();
    }

    /**
     * Regenerates the session ID.
     *
     * @param bool $deleteOldSession Whether to delete the old session data.
     * @return bool True on success or false on failure.
     */
    public function sessionRegenerateId(bool $deleteOldSession = false): bool
    {
        return session_regenerate_id($deleteOldSession);
    }
}