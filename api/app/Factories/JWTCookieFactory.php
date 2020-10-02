<?php

namespace App\Factories;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * @coversDefaultClass  \App\Factories\JWTCookieFactory
 */
class JWTCookieFactory
{
    public const COOKIE_NAME = 'token';

    /**
     * @param string $token The JWT token that will be stored in the cookie
     * @param string $apiUrl The API URL, from which we'll retrieve the domain
     * @param bool $isSecured Whether the cookie should be secured or not (not enabled by default to support local env)
     * @return Cookie
     */
    public static function make(string $token, string $apiUrl, bool $isSecured) : Cookie
    {
        return new Cookie(
            self::COOKIE_NAME,
            $token,
            strtotime('+4hours'),
            '/',
            parse_url($apiUrl, PHP_URL_HOST),
            $isSecured,
            true
        );
    }

    public static function makeExpired() : Cookie
    {
        return new Cookie(self::COOKIE_NAME, null);
    }
}
