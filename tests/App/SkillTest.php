<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SkillTest extends TestCase
{
    /**
     * @test
     *
     * Get all skill
     *
     * @return void
     */
    public function it_should_return_all_skill()
    {
        $this->get("skill", []);
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
     * No skill found
     */
    public function it_should_return_no_skill_found()
    {
        $this->get("skill", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
