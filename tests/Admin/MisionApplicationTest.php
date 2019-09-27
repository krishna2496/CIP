<?php
use Carbon\Carbon;

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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();
        $motivation = str_random(10);
        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = $motivation;
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save(); 
        
        $this->get('/missions/'.$missionApplication->mission_id.'/applications?search='.$motivation.'&order=ASC',
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
        $user->delete(); 
        $mission->delete(); 
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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = str_random(10);
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save(); 

        $this->get('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete();
        $user->delete(); 
        $mission->delete();  
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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->get('/missions/'.$mission->mission_id.'/applications/'.rand(10000000, 200000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $mission->delete(); 
    }

    /**
    * @test
    *
    * Return error for invalid mission id
    *
    * @return void
    */
    public function it_should_return_error_for_invalid_mission_id_to_get_application()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = str_random(10);
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save(); 

        $this->get('/missions/'.rand(10000000, 200000000).'/applications/'.$missionApplication->mission_application_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        $missionApplication->delete(); 
        $mission->delete(); 
        $user->delete(); 
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

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = str_random(10);
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save(); 
        
        $this->patch('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
        $missionApplication->delete();
        $mission->delete();
        $user->delete();
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

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = str_random(10);
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save();  

        $this->patch('/missions/'.rand(1000000, 2000000).'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $missionApplication->delete();
        $user->delete(); 
        $mission->delete();
    }

    /**
    * @test
    *
    * Return error for invalid argument for get all mission applications
    *
    * @return void
    */
    public function it_should_return_error_for_invalid_argument_for_mission_applications()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();
        $motivation = str_random(10);
        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = $motivation;
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save(); 
        
        $this->get('/missions/'.$missionApplication->mission_id.'/applications?search='.$motivation.'&order=test',
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $missionApplication->delete(); 
        $user->delete(); 
        $mission->delete(); 
    }

    /**
     * @test
     *
     * Return error for invalid status on update mission api
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_status_on_update_mission_application()
    {
        $params = [
                    "approval_status" => "test",
                ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = str_random(10);
        $missionApplication->approval_status = config('constants.application_status.PENDING');
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save();  

        $this->patch('/missions/'.$mission->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $missionApplication->delete();
    }

    /**
     * @test
     *
     * Return error on update mission api
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_application_id_on_update_mission_application()
    {
        $params = [
                    "approval_status" => "AUTOMATICALLY_APPROVED",
                ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $missionApplication = new App\Models\MissionApplication();

        $this->patch('/missions/'.$mission->mission_id.'/applications/'.rand(1000000, 2000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $mission->delete();
    }
}
