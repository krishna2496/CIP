<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CityTest extends TestCase
{
    /**
     * @test
     *
     * Get all city
     *
     * @return void
     */
    public function it_should_return_all_city()
    {
        $this->get("city", []);
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
     * No city found
     */
    public function it_should_return_no_city_found()
    {
        $this->get("city", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
