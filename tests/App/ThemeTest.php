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
    public function it_should_return_all_theme()
    {
        $this->get("theme", []);
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
     * No mission theme found
     */
    public function it_should_return_no_theme_found()
    {
        $this->get("theme", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
