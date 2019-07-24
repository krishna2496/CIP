<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class MissionApplicationTest extends TestCase
{
    /**
    * @test
    *
    * Get all mission applications
    *
    * @return void
    */
    public function it_should_return_mission_applications()
    {
        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 
        
        DB::setDefaultConnection('mysql');

        $this->get('/missions/'.$missionApplication->mission_id.'/applications', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
    }

    /**
    * @test
    *
    * Get all mission application
    *
    * @return void
    */
    public function it_should_return_mission_application()
    {
        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 
        
        DB::setDefaultConnection('mysql');

        $this->get('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
    }

    /**
    * @test
    *
    * Return error for invalid mission application
    *
    * @return void
    */
    public function it_should_return_error_for_invalid_mission_application()
    {
        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 
        
        DB::setDefaultConnection('mysql');

        $this->get('/missions/'.$missionApplication->mission_id.'/applications/'.rand(10000000, 200000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
    }

    /**
    * @test
    *
    * Return error for invalid mission id
    *
    * @return void
    */
    public function it_should_return_error_for_invalid_mission_id()
    {
        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 
        
        DB::setDefaultConnection('mysql');

        $this->get('/missions/'.rand(10000000, 200000000).'/applications/'.$missionApplication->mission_application_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
    }

    /**
     * @test
     *
     * Update mission api
     *
     * @return void
     */
    public function it_should_update_mission_application()
    {
        $params = [
                    "approval_status" => "AUTOMATICALLY_APPROVED",
                ];

        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 

        DB::setDefaultConnection('mysql');
        $this->patch('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
        $missionApplication->delete();
    }
    
    /**
     * @test
     *
     * Return error on update mission api
     *
     * @return void
     */
    public function it_should_return_error_on_update_mission_application()
    {
        $params = [
                    "approval_status" => "AUTOMATICALLY_APPROVED",
                ];

        DB::setDefaultConnection('tenant');
        $connection = 'tenant';
        $missionApplication = factory(\App\Models\MissionApplication::class)->make();
        $missionApplication->setConnection($connection);
        $missionApplication->save();
        $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
        $missionApplication->user_id = App\User::get()->random()->user_id;
        $missionApplication->update(); 

        DB::setDefaultConnection('mysql');
        $this->patch('/missions/'.rand(1000000, 2000000).'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
        $missionApplication->delete();
    }
}
