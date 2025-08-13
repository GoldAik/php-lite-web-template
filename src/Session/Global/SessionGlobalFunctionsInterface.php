<?php

declare(strict_types = 1);

namespace App\Session\Global;

interface SessionGlobalFunctionsInterface
{
    public function sessionStatus(): int;
    public function headersSent(?string &$filename = null, ?int &$line = null): bool;
    public function sessionSetCookieParams(array $params): bool;
    public function sessionSavePath(null|string $path): string|false;
    public function sessionStart(): bool;
    public function sessionWriteClose(): bool;
    public function sessionRegenerateId(bool $deleteOldSession = false): bool;
}