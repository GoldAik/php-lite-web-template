<?php

declare(strict_types = 1);

namespace App\Session;

use App\Session\DTO\Options;

class Session implements SessionInterface
{
    public function __construct(protected readonly Options $options) { }

    public function start(): void
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Session has already been started');
        }

        if (headers_sent($filename, $line)) {
            throw new \RuntimeException('Headers already sent');
        }

        session_set_cookie_params([
            'lifetime' => $this->options->lifetime,
            'path'     => $this->options->path,
            'secure'   => $this->options->secure,
            'httponly' => $this->options->httpOnly,
            'samesite' => $this->options->sameSite->value,
        ]);

        session_save_path($this->options->storagePath);

        session_start();
    }

    public function save(): void
    {
        session_write_close();
    }

    public function regenerate(bool $deleteOld = false): bool
    {
        return session_regenerate_id($deleteOld);
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