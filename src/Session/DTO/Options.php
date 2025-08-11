<?php

declare(strict_types = 1);

namespace App\Session\DTO;

use App\Session\Enum\SameSite;

readonly class Options
{
    public function __construct(
        public readonly string $name,
        public readonly int $lifetime = 60 * 30,
        public readonly string $path = '/',
        public readonly bool $secure = true,
        public readonly bool $httpOnly = true,
        public readonly SameSite $sameSite = SameSite::Lax,
        public readonly ?string $storagePath = null,
    ) { }
} 