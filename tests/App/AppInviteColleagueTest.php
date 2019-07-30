<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppInviteColleagueTest extends TestCase
{
    /**
     * @test
     *
     * It should validate user before invite
     *
     * @return void
     */
    public function it_should_validate_user_before_invite()
    {
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => $missionId,
            'to_user_id' => rand(1000000,2000000)
        ];
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
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
    }

    /**
     * @test
     *
     * It should validate mission before invite
     *
     * @return void
     */
    public function it_should_validate_mission_before_invite()
    {
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => rand(1000000,2000000)
        ];
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
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
    }

    /**
     * @test
     *
     * It should check user is already invited or not
     *
     * @return void
     */
    public function it_should_return_error_if_user_already_invited_for_mission()
    {
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        $missionInviteData = App\Models\MissionInvite::get()->random();
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => $missionInviteData->missionId,
            'to_user_id' => $missionInviteData->to_user_id
        ];
        
        $token = Helpers::getTestUserToken($missionInviteData->from_user_id);
        $this->post('/app/mission/invite', $params, ['token' => $token])
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
    }

    /**
     * @test
     *
     * It should validate user before invite
     *
     * @return void
     */
    public function it_should_invite_user_to_a_mission()
    {
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        $toUserId = App\User::where('user_id', '<>', $userId)->get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => $missionId,
            'to_user_id' => $toUserId
        ];
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'data' => [
                "mission_invite_id"
            ],
            'message',
        ]);  
        App\Models\MissionInvite::where(['mission_id' =>$missionId, 'to_user_id' => $toUserId, 'from_user_id' => $userId ])->take(1)->delete();  
    }
}
