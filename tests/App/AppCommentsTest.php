<?php
use Illuminate\Support\Facades\DB;
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($userId);
        $this->get('/app/mission/'.$missionId.'/comments', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($userId);
        $this->get('/app/mission/'.$missionId.'/comments', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
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
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($userId);
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
    }
}
