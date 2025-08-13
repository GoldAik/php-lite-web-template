<?php

declare(strict_types = 1);

namespace App\Session\Global;

class SessionGlobalFunctions implements SessionGlobalFunctionsInterface
{
    public function sessionStatus(): int
    {
        return session_status();
    }

    public function headersSent(?string &$filename = null, ?int &$line = null): bool
    {
        return headers_sent($filename, $line);
    }

    public function sessionSetCookieParams(array $params): bool
    {
        return session_set_cookie_params($params);
    }

    public function sessionSavePath(null|string $path): string|false
    {
        return session_save_path($path);
    }

    public function sessionStart(): bool
    {
        return session_start();
    }

    public function sessionWriteClose(): bool
    {
        return session_write_close();
    }

    public function sessionRegenerateId(bool $deleteOldSession = false): bool
    {
        return session_regenerate_id($deleteOldSession);
    }
}