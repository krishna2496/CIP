<?php
use App\Helpers\Helpers;
use App\User;
use App\Models\Country;
use App\Models\City;
use App\Models\MissionTheme;
use App\Models\Availability;

class TimesheetTest extends TestCase
{
    /**
     * @test
     *
     * It should list timesheet entries of user
     *
     * @return void
     */
    public function timesheet_it_should_return_timesheet_entries_of_user()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        $themeId = MissionTheme::first()->mission_theme_id;
        $availabilityId = Availability::first()->availability_id;

        // Create request for mission create
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => str_random(100)
            ],
            "location" => [
                "city_id" => $cityId,
                "country_code" => $countryDetail->ISO
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => str_random(10),
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => $themeId,
            "availability_id" => $availabilityId
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $missionId = json_decode($response->response->getContent())->data->mission_id;

        $params = [
                'mission_id' => $missionId,
                'motivation' => str_random(10),
                'availability_id' => 1
            ];

        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        // Creating mission application for created mission
        $application = $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
        
        $missionApplicationId = json_decode($response->response->getContent())->data->mission_application_id;
        
        // Update mission application status as approved
        App\Models\MissionApplication::where("mission_application_id", $missionApplicationId)
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $missionId,
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' => []
        ];

        DB::setDefaultConnection('mysql');

        // Creating timesheet entry for created mission
        $timesheet = $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
          ]);

        $timeSheetId = json_decode($timesheet->response->getContent())->data->timesheet_id;

        \App\Models\Timesheet::where('timesheet_id', $timeSheetId)->update(
            [
                'status' => config('constants.timesheet_status.AUTOMATICALLY_APPROVED')
            ]
        );
        
        DB::setDefaultConnection('mysql');
        $response = $this->get('timesheet/' . $user->user_id, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        \App\Models\Timesheet::where('timesheet_id', $timeSheetId)->delete();
        
        DB::setDefaultConnection('mysql');
        $response = $this->get('timesheet/' . $user->user_id, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        $user->delete();
    }

    /**
     * @test
     *
     * It should list timesheet entries of user
     *
     * @return void
     */
    public function timesheet_it_should_return_user_not_found_when_timesheet_entries_of_user()
    {
        $userId = rand(500000000, 5000000000000);
        $response = $this->get('timesheet/' . $userId, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should list timesheet entries of user
     *
     * @return void
     */
    public function timesheet_it_should_update_timesheet_entries_of_user()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        $themeId = MissionTheme::first()->mission_theme_id;
        $availabilityId = Availability::first()->availability_id;

        // Create request for mission create
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => str_random(100)
            ],
            "location" => [
                "city_id" => $cityId,
                "country_code" => $countryDetail->ISO
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => str_random(10),
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => $themeId,
            "availability_id" => $availabilityId
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $missionId = json_decode($response->response->getContent())->data->mission_id;

        $params = [
                'mission_id' => $missionId,
                'motivation' => str_random(10),
                'availability_id' => 1
            ];

        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        // Creating mission application for created mission
        $application = $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
        
        $missionApplicationId = json_decode($response->response->getContent())->data->mission_application_id;
        
        // Update mission application status as approved
        App\Models\MissionApplication::where("mission_application_id", $missionApplicationId)
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $missionId,
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' => []
        ];

        DB::setDefaultConnection('mysql');

        // Creating timesheet entry for created mission
        $timesheet = $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
          ]);

        $timeSheetId = json_decode($timesheet->response->getContent())->data->timesheet_id;

        \App\Models\Timesheet::where('timesheet_id', $timeSheetId)->update(
            [
                'status' => config('constants.timesheet_status.AUTOMATICALLY_APPROVED')
            ]
        );
        
        DB::setDefaultConnection('mysql');
        
        $params = [
            "status" => config('constants.timesheet_status.PENDING')
        ];
        
        $this->patch('timesheet/' . $timeSheetId, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        $invalidParam = [
            "status" => rand(80000, 8000000)
        ];
        DB::setDefaultConnection('mysql');
        $this->patch('timesheet/' . $timeSheetId, $invalidParam, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        $timeSheetId = rand(5000000000, 50000000000);
        DB::setDefaultConnection('mysql');
        $res = $this->patch('timesheet/' . $timeSheetId, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(404);

        $user->delete();
    }

    /**
     * @test
     *
     * It should list timesheet entries of user
     *
     * @return void
     */
    public function timesheet_it_should_return_timesheet_entries_of_user_with_search()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Get country and city id for mission create
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        $themeId = MissionTheme::first()->mission_theme_id;
        $availabilityId = Availability::first()->availability_id;
        \DB::setDefaultConnection('mysql');


        // Create request for mission create
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => str_random(100)
            ],
            "location" => [
                "city_id" => $cityId,
                "country_code" => $countryDetail->ISO
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => str_random(10),
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => $themeId,
            "availability_id" => $availabilityId
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $missionId = json_decode($response->response->getContent())->data->mission_id;

        $params = [
                'mission_id' => $missionId,
                'motivation' => str_random(10),
                'availability_id' => 1
            ];

        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        // Creating mission application for created mission
        $application = $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
        
        $missionApplicationId = json_decode($response->response->getContent())->data->mission_application_id;
        
        // Update mission application status as approved
        App\Models\MissionApplication::where("mission_application_id", $missionApplicationId)
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $missionId,
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' => []
        ];

        DB::setDefaultConnection('mysql');

        // Creating timesheet entry for created mission
        $timesheet = $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
          ]);

        $timeSheetId = json_decode($timesheet->response->getContent())->data->timesheet_id;
        \App\Models\Timesheet::where('timesheet_id', $timeSheetId)->update(
            [
                'status' => config('constants.timesheet_status.AUTOMATICALLY_APPROVED')
            ]
        );
        
        $status = config('constants.timesheet_status.AUTOMATICALLY_APPROVED');
        $statusId = Config('constants.timesheet_status.' . $status);
        DB::setDefaultConnection('mysql');
        $response = $this->get('timesheet/' . $user->user_id . '?status=' . $statusId, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        \App\Models\Timesheet::where('timesheet_id', $timeSheetId)->delete();
        
        DB::setDefaultConnection('mysql');
        $response = $this->get('timesheet/' . $user->user_id, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        $response = $this->get('timesheet/' . $user->user_id . '?type=' . config("constants.mission_type.TIME"), ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        $user->delete();
    }
}
