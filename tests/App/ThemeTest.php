<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ThemeTest extends TestCase
{
    /**
     * Get all mission theme
     *
     * @return void
     */
    public function testShouldReturnAllTheme()
    {
        $this->get("theme", []);
        $this->seeStatusCode(200);
       
        $this->seeJsonStructure([
            "status",
            'data',
            "message"
        ]);
    }
}
