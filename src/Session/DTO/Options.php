<?php

declare(strict_types = 1);

namespace App\Session\DTO;

use App\Session\Enum\SameSite;

/**
 * Class Options
 * 
 * Data Transfer Object (DTO) for session configuration options.
 * 
 * @package App\Session\DTO
 */
readonly class Options
{
    /**
     * Constructor for Options.
     * 
     * @param string $name Name of the session.
     * @param int $lifetime Session lifetime in seconds. Default is 30 minutes.
     * @param string $path Path on the server in which the session will be available. Default is '/'.
     * @param bool $secure Whether the session should only be transmitted over secure HTTPS connections. Default is true.
     * @param bool $httpOnly Whether the session cookie will be accessible only through the HTTP protocol. Default is true.
     * @param SameSite $sameSite Controls when cookies are sent. Default is SameSite::Lax.
     * @param string|null $storagePath Optional. Path where session data will be stored.
     */
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