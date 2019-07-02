<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CountryTest extends TestCase
{
    /**
     * @test
     *
     * Get all country
     *
     * @return void
     */
    public function it_should_return_all_country()
    {
        $this->get("country", []);
        $this->seeStatusCode(200);
       
        $this->seeJsonStructure([
            "status",
            'data',
            "message"
        ]);
    }

    /**
     * @test
     *
     * No country found
     */
    public function it_should_return_no_country_found()
    {
        $this->get("country", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
