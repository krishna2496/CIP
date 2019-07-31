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
