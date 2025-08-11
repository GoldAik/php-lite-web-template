<?php

declare(strict_types = 1);

namespace App\Session;


interface SessionInterface
{
    public function start(): void;
    public function save(): void;
    public function regenerate(): bool;

    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function unset(string $key): void;
}