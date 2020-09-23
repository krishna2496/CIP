<?php

namespace Tests\Unit\Factories;

use App\Factories\JWTCookieFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class JWTCookieFactoryTest extends TestCase
{
    public function testMake()
    {
        $token = '1234567890';
        $referer = 'http://somedomain.com:1234/foo';
        $expectedDomain = 'somedomain.com';
        $expectedPath = '/';
        $isSecured = true;

        $actual = JWTCookieFactory::make($token, $referer, $isSecured);
        $this->assertInstanceOf(Cookie::class, $actual);
        $this->assertEquals($expectedDomain, $actual->getDomain());
        $this->assertEquals($expectedPath, $actual->getPath());
        $this->assertEquals($token, $actual->getValue());
        $this->assertTrue($actual->isSecure());
        $this->assertTrue($actual->isHttpOnly());
    }

    public function testMakeWithNonSecureOption()
    {
        $token = '1234567890';
        $referer = 'http://somedomain.com:1234/foo';
        $isSecured = false;

        $actual = JWTCookieFactory::make($token, $referer, $isSecured);
        $this->assertFalse($actual->isSecure());
    }

    public function testMakeExpired()
    {
        $expectedExpirationTime = 0;
        $actual = JWTCookieFactory::makeExpired();
        $this->assertInstanceOf(Cookie::class, $actual);
        $this->assertEquals($expectedExpirationTime, $actual->getExpiresTime());
    }
}
