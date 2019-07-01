<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CountryTest extends TestCase
{
    /**
     * Get all country
     *
     * @return void
     */
    public function testShouldReturnAllCountry()
    {
        $this->get("country", []);
        $this->seeStatusCode(200);
       
        $this->seeJsonStructure([
            "status",
            'data',
            "message"
        ]);
    }
}
