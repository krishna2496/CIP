<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SkillTest extends TestCase
{
    /**
     * Get all city
     *
     * @return void
     */
    public function testShouldReturnAllSkill()
    {
        $this->get("skill", []);
        $this->seeStatusCode(200);
       
        $this->seeJsonStructure([
            "status",
            'data',
            "message"
        ]);
    }
}
