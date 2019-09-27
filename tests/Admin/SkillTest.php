<?php

class SkillTest extends TestCase
{
    /**
     * @test
     *
     * Create skill
     *
     * @return void
     */
    public function it_should_create_skill()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if user do not enter skill name
     *
     * @return void
     */
    public function it_should_return_error_if_skill_name_is_blank()
    {
        $params = [        
            "skill_name" => "",
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return error if user enter wrong parent skill
     *
     * @return void
     */
    public function it_should_return_error_if_user_enter_wrong_parent_skill()
    {
        $params = [        
            "skill_name" => str_random(20),
            "parent_skill" => rand(1000000, 5000000),
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Get all skills
     *
     * @return void
     */
    public function it_should_return_all_skills_for_admin()
    {
        $this->get('entities/skills', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
               "*" => [
                    "skill_id",
                    "skill_name",
                    "translations"
                ]
            ],
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get a skill by skill id
     *
     * @return void
     */
    public function it_should_return_a_skill_for_admin_by_skill_id()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;
        DB::setDefaultConnection('mysql');

        $this->get('entities/skills/'.$skillId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "skill_id",
                "skill_name",
                "translations"
            ],
            "message"
        ]);
        App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if skill id is wrong
     *
     * @return void
     */
    public function it_should_return_error_if_skill_id_is_wrong()
    {
        $this->get('entities/skills/'.rand(1000000,2000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * It should update skill
     *
     * @return void
     */
    public function it_should_update_skill()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "parent_skill" => 0
        ];
        
        $this->patch('entities/skills/'.$skillId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Skill::where("skill_id", $skillId)->delete();
    }

    /**
     * @test
     *
     * It should return error for blank skill name
     *
     * @return void
     */
    public function it_should_return_error_for_update_skill_blank_skill_name()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        
        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "skill_name" => ""
        ];
        
        $this->patch('entities/skills/'.$skillId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        App\Models\Skill::where("skill_id", $skillId)->delete();
    }

    /**
     * @test
     *
     * It should return error if user enter wrong parent skill
     *
     * @return void
     */
    public function it_should_return_error_for_update_skill_for_wrong_parent_skill()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        
        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "parent_skill" => rand(1000000, 5000000)
        ];
        
        $this->patch('entities/skills/'.$skillId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        App\Models\Skill::where("skill_id", $skillId)->delete();
    }

    /**
     * @test
     *
     * It should return error if user enter wrong skill id
     *
     * @return void
     */
    public function it_should_return_error_for_wrong_skill_id()
    {   
        $params = [        
            "skill_name" => str_random(20),
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->patch('entities/skills/'.rand(1000000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * It should delete skill
     *
     * @return void
     */
    public function it_should_delete_skill()
    {
        $skillName = str_random(20);
        $params = [        
            "skill_name" => $skillName,
            "parent_skill" => 0,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "skill testing"
                ]
            ]
        ];

        $this->post("entities/skills", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $skill = App\Models\Skill::where("skill_name", $skillName)->orderBy("skill_id", "DESC")->take(1)->get();
        $skillId = $skill[0]->skill_id;
        DB::setDefaultConnection('mysql');
        
        $this->delete('entities/skills/'.$skillId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * It should return error for invalid skill id for delete skill
     *
     * @return void
     */
    public function it_should_return_error_for_delete_skill_for_invalid_skill_id()
    {   
        $this->delete('entities/skills/'.rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return invalid argument error for get all skills
     *
     * @return void
     */
    public function it_should_return_for_invalid_argument_for_get_all_skills_for_admin()
    {
        $this->get('entities/skills?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
    }
}
