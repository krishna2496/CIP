<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppMissionMediaTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission media by mission id
     *
     * @return void
     */
    public function it_should_return_all_media_by_mission_id()
    {
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getTestUserToken($userId);
        $this->get('/app/mission-media/'.$missionId, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_media_id",
                    "media_name",
                    "media_type",
                    "media_path",
                    "default"
                ],
            ],
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
    public function it_should_return_error_for_invalid_mission_id_for_get_media()
    {
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getTestUserToken($userId);
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
