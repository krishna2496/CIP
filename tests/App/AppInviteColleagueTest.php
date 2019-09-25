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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'mission_id' => $mission->mission_id,
            'to_user_id' => rand(1000000, 2000000)
        ];
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $user->delete();
        $mission->delete();
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
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'mission_id' => rand(1000000, 2000000)
        ];
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
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
        $user->delete();
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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $toUser = factory(\App\User::class)->make();
        $toUser->setConnection($connection);
        $toUser->save();

        $params = [
            'mission_id' => $mission->mission_id,
            'to_user_id' => $toUser->user_id
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('/app/mission/invite', $params, ['token' => $token]);

        DB::setDefaultConnection('mysql');

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
        App\Models\MissionInvite::where(['mission_id' =>$mission->mission_id, 'to_user_id' => $toUser->user_id, 'from_user_id' => $user->user_id ])->take(1)->delete();
        $user->delete();
        $toUser->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should validate user before invite
     *
     * @return void
     */
    public function it_should_invite_user_to_a_mission_and_send_notification()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $toUser = factory(\App\User::class)->make();
        $toUser->setConnection($connection);
        $toUser->save();

        $notification = factory(\App\Models\UserNotification::class)->make();
        $notification->setConnection($connection);
        $notification->user_id = $toUser->user_id;
        $notification->save();

        $missionLanguage = factory(\App\Models\MissionLanguage::class)->make();
        $missionLanguage->setConnection($connection);
        $missionLanguage->mission_id = $mission->mission_id;
        $missionLanguage->save();

        $params = [
            'mission_id' => $mission->mission_id,
            'to_user_id' => $toUser->user_id
        ];
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'data' => [
                "mission_invite_id"
            ],
            'message',
        ]);
        App\Models\MissionInvite::where(['mission_id' =>$mission->mission_id, 'to_user_id' => $toUser->user_id, 'from_user_id' => $user->user_id ])->take(1)->delete();
        $user->delete();
        $toUser->delete();
        $mission->delete();
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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $toUser = factory(\App\User::class)->make();
        $toUser->setConnection($connection);
        $toUser->save();

        DB::setDefaultConnection('tenant');
        $settings = App\Models\TenantSetting::where(['setting_id' =>27])->get();
        App\Models\TenantActivatedSetting::where(['tenant_setting_id' => $settings[0]['tenant_setting_id']])->delete();
        App\Models\TenantSetting::where(['setting_id' => 27])->delete();
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => $mission->mission_id,
            'to_user_id' => $toUser->user_id
        ];
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'data' => [
                "mission_invite_id"
            ],
            'message',
        ]);
        App\Models\MissionInvite::where(['mission_id' =>$mission->mission_id, 'to_user_id' => $toUser->user_id, 'from_user_id' => $user->user_id ])->take(1)->delete();
        $user->delete();
        $toUser->delete();
        $mission->delete();
        $setting = App\Models\TenantSetting::create(['setting_id' =>27]);
        App\Models\TenantActivatedSetting::create(['tenant_setting_id' =>$setting->tenant_setting_id]);
    }
}
