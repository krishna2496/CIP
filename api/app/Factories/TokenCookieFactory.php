<?php

namespace App\Factories;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * @coversDefaultClass  \App\Factories\TokenCookieFactory
 */
class TokenCookieFactory
{
    public const TOKEN_COOKIE_NAME = 'token';

    /**
     * @param string $token The JWT token that will be stored in the cookie
     * @param string $referer Referer extracted from the request, from which we'll retrieve the domain
     * @param bool $isSecured Whether the cookie should be secured or not (not enabled by default to support local env)
     * @return Cookie
     */
    public static function make(string $token, string $referer, bool $isSecured) : Cookie
    {
        return new Cookie(
            TokenCookieFactory::TOKEN_COOKIE_NAME,
            $token,
            strtotime('+4hours'),
            '/',
            parse_url($referer, PHP_URL_HOST),
            $isSecured,
            true
        );
    }

    public static function makeExpired() : Cookie
    {
        return new Cookie(self::TOKEN_COOKIE_NAME, 0);
    }
}
