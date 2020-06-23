<?php
use Carbon\Carbon;
use App\Models\Mission;
use App\Models\CityLanguage;
use App\Models\CountryLanguage;
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
            '/missions/'.$missionApplication->mission_id.'/applications?search='.$motivation.'&order=ASC',
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

        $this->get('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('/missions/'.$mission->mission_id.'/applications/'.rand(10000000, 200000000), ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('/missions/'.rand(10000000, 200000000).'/applications/'.$missionApplication->mission_application_id, ['Authorization' => Helpers::getBasicAuth()])
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
        
        $this->patch('/missions/'.$missionApplication->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->patch('/missions/'.rand(1000000, 2000000).'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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
            '/missions/'.$missionApplication->mission_id.'/applications?search='.$motivation.'&order=test',
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

        $this->patch('/missions/'.$mission->mission_id.'/applications/'.$missionApplication->mission_application_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->patch('/missions/'.$mission->mission_id.'/applications/'.rand(1000000, 2000000), $params, ['Authorization' => Helpers::getBasicAuth()])
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
            '/missions/'.$missionApplication->mission_id.'/applications?search='.$motivation.'&order=ASC&status='.$status.'&user_id='.$user->user_id.'&type='.config("constants.mission_type.GOAL"),
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
    * The return data must have an existing created mission with is_virtual as zero and one
    *
    * @return void
    */
    public function it_should_return_both_mission_with_virtual_one_and_zero()
    {
        $records = $this->createMissionApplicationData(null);

        $both = array_filter($records['response']->data, function ($item) use ($records) {
            return in_array($item->mission_id, [
                $records['mission_zero']->mission_id,
                $records['mission_one']->mission_id
            ]);
        });

        $this->assertTrue(count($both) === 2);
    }

    /**
    * @test
    *
    * The return data must have an existing created mission with is_virtual as one
    *
    * @return void
    */
    public function it_should_return_both_mission_with_virtual_one()
    {
        $records = $this->createMissionApplicationData('?filter[isVirtual]=1');

        $both = array_filter($records['response']->data, function ($item) use ($records) {
            return in_array($item->mission_id, [
                $records['mission_one']->mission_id
            ]);
        });

        $this->assertTrue(count($both) === 1);
    }

    /**
    * @test
    *
    * The return data must have an existing created mission with is_virtual as zero
    *
    * @return void
    */
    public function it_should_return_both_mission_with_virtual_zero()
    {
        $records = $this->createMissionApplicationData('?filter[isVirtual]=0');

        $both = array_filter($records['response']->data, function ($item) use ($records) {
            return in_array($item->mission_id, [
                $records['mission_zero']->mission_id
            ]);
        });

        $this->assertTrue(count($both) === 1);
    }

    private function createMissionApplicationData($params = null)
    {
        $authentication = [
            'Authorization' => Helpers::getBasicAuth()
        ];

        $approvalStatus = config('constants.application_status.PENDING');
        $connection = 'tenant';

        // Create first mission and mission lang with is_virtual as 0
        $mission = factory(\App\Models\Mission::class)->make([
            'is_virtual' => 0
        ]);
        $mission->setConnection($connection);
        $mission->save();

        $missionLang = factory(\App\Models\MissionLanguage::class)->make([
            'mission_id' => $mission->mission_id
        ]);
        $missionLang->setConnection($connection);
        $missionLang->save();

        // Create first mission and mission lang  with is_virtual as 1
        $mission1 = factory(\App\Models\Mission::class)->make([
            'is_virtual' => 1
        ]);
        $mission1->setConnection($connection);
        $mission1->save();

        $missionLang1 = factory(\App\Models\MissionLanguage::class)->make([
            'mission_id' => $mission1->mission_id
        ]);
        $missionLang1->setConnection($connection);
        $missionLang1->save();

        $city = \DB::connection($connection);
        $checkCity = $city->select('SELECT * FROM city_language WHERE city_id = ? AND language_id = ?', [
            $mission->city_id,
            1
        ]);

        if (empty($checkCity)) {
            // Create city language for missions
            $cityLang = factory(CityLanguage::class)->make([
                'city_id' => $mission->city_id
            ]);
            $cityLang->setConnection($connection);
            $cityLang->save();
            $cityId = $cityLang->city_language_id;
        } else {
            $cityId = $checkCity[0]->city_language_id;
        }

        $country = \DB::connection($connection);
        $checkCountry = $country->select('SELECT * FROM country_language WHERE country_id = ? AND language_id = ?', [
            $mission->country_id,
            1
        ]);

        if (empty($checkCountry)) {
            // Create country language for missions
            $countryLang = factory(CountryLanguage::class)->make([
                'country_id' => $mission->country_id
            ]);
            $countryLang->setConnection($connection);
            $countryLang->save();
            $countryId = $countryLang->country_language_id;
        } else {
            $countryId = $checkCountry[0]->country_language_id;
        }

        // Create first user applied for the first mission
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Create first mission application with mission then user ids
        $application = factory(\App\Models\MissionApplication::class)->make([
            'mission_id' => $mission->mission_id,
            'user_id' => $user->user_id,
            'approval_status' => $approvalStatus
        ]);
        $application->setConnection($connection);
        $application->save();

        // Create first user applied for the first mission
        $user1 = factory(\App\User::class)->make();
        $user1->setConnection($connection);
        $user1->save();

        // Create first mission application with mission then user ids
        $application1 = factory(\App\Models\MissionApplication::class)->make([
            'mission_id' => $mission1->mission_id,
            'user_id' => $user1->user_id,
            'approval_status' => $approvalStatus
        ]);
        $application1->setConnection($connection);
        $application1->save();

        $applications = $this->get("/missions/applications/details$params", $authentication)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'data',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'next_url'
                ],
            ]);

        // Delete all created datas for testing
        Mission::whereIn('mission_id', [
            $mission->mission_id,
            $mission1->mission_id
        ])->forceDelete();
        CityLanguage::where('city_language_id', $cityId)->forceDelete();
        CountryLanguage::where('country_language_id', $countryId)->forceDelete();

        return [
            'response' => json_decode($applications->response->getContent()),
            'mission_zero' => $mission,
            'mission_one' => $mission1
        ];
    }
}
