<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use App\Models\Mission;
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
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(422);    
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
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(422);     
    }

    /**
     * @test
     *
     * It should check user is already invited or not
     *
     * @return void
     */
    public function it_should_check_user_already_invited_or_not()
    {
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        $toUserId = App\User::where('user_id', '<>', $userId)->get()->random()->user_id;
        $missionId = App\Models\Mission::get()->random()->mission_id;
        DB::setDefaultConnection('mysql');

        $params = [
            'mission_id' => $missionId,
            'to_user_id' => $toUserId
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getTestUserToken($userId);
        $this->post('/app/mission/invite', $params, ['token' => $token])
        ->seeStatusCode(422);     
    }
}
