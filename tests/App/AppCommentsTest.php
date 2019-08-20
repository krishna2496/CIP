<?php
use App\Helpers\Helpers;

class AppCommentsTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission related comments by mission id
     *
     * @return void
     */
    public function it_should_return_all_comments_by_mission_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/mission/'.$mission->mission_id.'/comments', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for no comments found by mission id
     *
     * @return void
     */
    public function it_should_return_no_comments_found_by_mission_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/mission/'.$mission->mission_id.'/comments', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_get_comments()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/mission/'.$missionId.'/comments', ['token' => $token])
        ->seeStatusCode(404)
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
        $user->delete();
    }

    /**
     * @test
     *
     * Add Comment
     *
     * @return void
     */
    public function it_should_add_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error for invalid mission id
     * 
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_add_comment()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => rand(1000000, 5000000)
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
        ->seeStatusCode(422)
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
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if comment field is blank
     * 
     * @return void
     */
    public function it_should_return_error_if_comment_is_blank()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $params = [
            "comment" => '',
            "mission_id" => $mission->mission_id
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
        ->seeStatusCode(422)
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
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if comment field exceeds maximum character
     * 
     * @return void
     */
    public function it_should_return_error_if_comments_exceeds_maximum_character_validation()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $params = [
            "comment" => '',
            "mission_id" => $mission->mission_id
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
        ->seeStatusCode(422)
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
        $user->delete();
        $mission->delete();
    }

}
