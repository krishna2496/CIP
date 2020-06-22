<?php
use Carbon\Carbon;
use App\Helpers\Helpers;

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
        
        $this->get(
            '/missions/' . $missionApplication->mission_id . '/applications?search=' . $motivation . '&order=ASC',
            ['Authorization' => Helpers::getBasicAuth()]
        )
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

        $this->get('/missions/' . $missionApplication->mission_id . '/applications/' . $missionApplication->mission_application_id, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('/missions/' . $mission->mission_id . '/applications/' . rand(10000000, 200000000), ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('/missions/' . rand(10000000, 200000000) . '/applications/' . $missionApplication->mission_application_id, ['Authorization' => Helpers::getBasicAuth()])
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
        
        $this->patch('/missions/' . $missionApplication->mission_id . '/applications/' . $missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->patch('/missions/' . rand(1000000, 2000000) . '/applications/' . $missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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
        
        $this->get(
            '/missions/' . $missionApplication->mission_id . '/applications?search=' . $motivation . '&order=test',
            ['Authorization' => Helpers::getBasicAuth()]
        )
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

        $this->patch('/missions/' . $mission->mission_id . '/applications/' . $missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->patch('/missions/' . $mission->mission_id . '/applications/' . rand(1000000, 2000000), $params, ['Authorization' => Helpers::getBasicAuth()])
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

    /**
    * @test
    *
    * Get all mission applications
    *
    * @return void
    */
    public function it_should_return_mission_applications_with_search()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $status = config('constants.application_status.PENDING');
        $missionApplication = new App\Models\MissionApplication();
        $motivation = str_random(10);
        $missionApplication->setConnection($connection);
        $missionApplication->mission_id = $mission->mission_id;
        $missionApplication->user_id = $user->user_id;
        $missionApplication->availability_id = 1;
        $missionApplication->motivation = $motivation;
        $missionApplication->approval_status = $status;
        $missionApplication->applied_at = Carbon::now();
        $missionApplication->save();
        
        $this->get(
            '/missions/' . $missionApplication->mission_id . '/applications?search=' . $motivation . '&order=ASC&status=' . $status . '&user_id=' . $user->user_id . '&type=' . config("constants.mission_type.GOAL"),
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(200);
        $missionApplication->delete();
        $user->delete();
        $mission->delete();
    }
}
