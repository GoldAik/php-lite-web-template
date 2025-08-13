<?php

declare(strict_types = 1);

namespace App\Session\Enum;

/**
 * Enum SameSite
 * 
 * Represents the possible values for the SameSite attribute of cookies.
 * 
 * @package App\Session\Enum
 */
enum SameSite: string
{
    /**
     * Cookies will be sent with cross-site requests.
     */
    case None = 'none';

    /**
     * Cookies will be sent with same-site requests, but with some cross-site restrictions.
     */
    case Lax = 'lax';

    /**
     * Cookies will only be sent with same-site requests.
     */
    case Strict = 'strict';
}