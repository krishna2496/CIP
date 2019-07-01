<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CityTest extends TestCase
{
    /**
     * Get all city
     *
     * @return void
     */
    public function testShouldReturnAllCity()
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
     * No city found
     */
    public function testShouldReturnNoCityFound()
    {
        $this->get("city", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
