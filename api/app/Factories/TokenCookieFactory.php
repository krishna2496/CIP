<?php

namespace App\Factories;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * @coversDefaultClass  \App\Factories\TokenCookieFactory
 */
class TokenCookieFactory
{
    public const TOKEN_COOKIE_NAME = 'token';

    public static function make() : Cookie
    {
    }

    public static function makeExpired() : Cookie
    {
        return new Cookie(self::TOKEN_COOKIE_NAME, 0);
    }
}
