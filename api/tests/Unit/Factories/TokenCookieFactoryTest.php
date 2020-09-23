<?php

namespace Tests\Unit\Factories;

use App\Factories\TokenCookieFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class TokenCookieFactoryTest extends TestCase
{
    public function testMakeExpired()
    {
        $expected = new Cookie(
            'cookiename',
            0
        );

        $actual = TokenCookieFactory::makeExpired();
        $this->assertEquals($expected->getExpiresTime(), $actual->getExpiresTime());
    }


}
