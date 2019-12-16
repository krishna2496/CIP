<?php
use App\Helpers\Helpers;
use Carbon\Carbon;

class AppVolunteeringHistoryTest extends TestCase
{
    /**
     * @test
     *
     * Get total user's timesheet history hours, per theme
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_volunteering_history_total_hours_per_theme()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id; 
        
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
            "total_seats" => rand(10, 100),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => App\Models\MissionTheme::first()->mission_theme_id,
            "availability_id" => App\Models\Availability::first()->availability_id
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
            'documents[]' =>[]
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
                'status_id' => \App\Models\TimesheetStatus::
                where('status', config('constants.timesheet_status.AUTOMATICALLY_APPROVED'))->first()->timesheet_status_id
            ]
        );
        DB::setDefaultConnection('mysql');
        // Get history of total hours spent on specific theme        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.theme'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "data" => [
                    "*" => [
                        "mission_theme_id",
                        "theme_name",
                        "total_minutes"
                    ],
                ],
                "message"
            ]
        );

        DB::setDefaultConnection('mysql');
        // Assert time mission history
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.time-mission'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "data" => [
                    "*" => [
                        "mission_id",
                        "organisation_name",
                        "title",
                        "hours"
                    ],
                ],
                "message"
            ]
        );
        
        DB::setDefaultConnection('mysql');
        // For specific year
        $response = $this->get('/app/volunteer/history/theme?year='.Carbon::now()->format('Y'), ['token' => $token])
        ->seeStatusCode(200);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return no data found for timesheet history hours, per theme
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_no_data_found_for_volunteering_history_total_hours_per_theme()
    {
        $connection = 'tenant';
        
        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get history of total hours spent on specific theme        
        \DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.theme'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );

        \DB::setDefaultConnection('mysql');

        // Assert time mission history not found
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.time-mission'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );

        \DB::setDefaultConnection('mysql');
        // Assert export report 
        $response = $this->get(route('app.volunteer.history.time-mission.export'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );

        $user->delete();


        
    }

    /**
     * @test
     *
     * It should return error for unautheorized token for timehseet history hours, per theme
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_error_unauthorized_for_volunteering_history_total_hours_per_theme()
    {
        $connection = 'tenant';
        
        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');
        
        // Get history of total hours spent on specific theme        
        $token = Helpers::getJwtToken($user->user_id, str_random('5'));  
        \DB::setDefaultConnection('mysql');
        $response = $this->get(route('app.volunteer.history.theme'), ['token' => $token])
        ->seeStatusCode(401);
        $user->delete();
    }

    /**
     * @test
     *
     * Export user's timesheet history hours, per theme
     *
     * @return void
     */
    public function app_volunteering_history_it_should_export_volunteering_history_total_hours_per_theme()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id; 
        
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
            "total_seats" => rand(10, 100),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => App\Models\MissionTheme::first()->mission_theme_id,
            "availability_id" => App\Models\Availability::first()->availability_id
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
            'documents[]' =>[]
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
                'status_id' => \App\Models\TimesheetStatus::
                where('status', config('constants.timesheet_status.AUTOMATICALLY_APPROVED'))->first()->timesheet_status_id
            ]
        );

        \DB::setDefaultConnection('mysql');

        // Get history of total hours spent on specific theme        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.time-mission.export'), ['token' => $token])
        ->seeStatusCode(200);
        $user->delete();
    }

    /**
     * @test
     *
     * Get total user's timesheet history hours, per skill
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_volunteering_history_total_hours_per_skill()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id; 
        
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

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
            "total_seats" => rand(10, 100),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => App\Models\MissionTheme::first()->mission_theme_id,
            "availability_id" => App\Models\Availability::first()->availability_id,
            "skills" => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
            'documents[]' =>[]
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
                'status_id' => \App\Models\TimesheetStatus::
                where('status', config('constants.timesheet_status.AUTOMATICALLY_APPROVED'))->first()->timesheet_status_id
            ]
        );

        DB::setDefaultConnection('mysql');

        // Get history of total hours spent on specific theme        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.skill'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "data" => [
                    "*" => [
                        "skill_id",
                        "skill_name",
                        "total_minutes"
                    ],
                ],
                "message"
            ]
        );
        DB::setDefaultConnection('mysql');
        // For specific year
        $response = $this->get('/app/volunteer/history/skill?year='.Carbon::now()->format('Y'), ['token' => $token])
        ->seeStatusCode(200);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return no data found for timesheet history hours, per skill
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_no_data_found_for_volunteering_history_total_hours_per_skill()
    {
        $connection = 'tenant';
        
        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get history of total hours spent on specific skill        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        \DB::setDefaultConnection('mysql');
        $response = $this->get(route('app.volunteer.history.skill'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     *
     * It should return error for unautheorized token for timehseet history hours, per skill
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_error_unauthorized_for_volunteering_history_total_hours_per_skill()
    {
        $connection = 'tenant';
        
        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');
        
        // Get history of total hours spent on specific skill        
        $token = Helpers::getJwtToken($user->user_id, str_random('5'));   

        \DB::setDefaultConnection('mysql');

        $response = $this->get(route('app.volunteer.history.skill'), ['token' => $token])
        ->seeStatusCode(401);
        
        $user->delete();
    }

    /**
     * @test
     *
     * Export user's timesheet history hours, per skill
     *
     * @return void
     */
    public function app_volunteering_history_it_should_export_volunteering_history_total_hours_per_skill()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id; 
        
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

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
            "total_seats" => rand(10, 100),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => App\Models\MissionTheme::first()->mission_theme_id,
            "availability_id" => App\Models\Availability::first()->availability_id,
            "skills" => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
            'documents[]' =>[]
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
                'status_id' => \App\Models\TimesheetStatus::
                where('status', config('constants.timesheet_status.AUTOMATICALLY_APPROVED'))->first()->timesheet_status_id
            ]
        );

        \DB::setDefaultConnection('mysql');
        // Get history of total hours spent on specific theme        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $response = $this->get(route('app.volunteer.history.theme'), ['token' => $token])
        ->seeStatusCode(200);        
        $user->delete();
    }

    /**
     * @test
     *
     * It should return volunteering history total hours per goal mission with export.
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_volunteering_history_total_hours_per_goal_mission_with_export()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        \DB::setDefaultConnection('tenant');

        // Get country and city id for mission create
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id; 
        
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
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(10, 100),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => App\Models\MissionTheme::first()->mission_theme_id,
            "availability_id" => App\Models\Availability::first()->availability_id
        ];

        \DB::setDefaultConnection('mysql');

        // Creating mission
        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
            'action' => rand(1, 5),            
            'documents[]' =>[]
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
                'status_id' => \App\Models\TimesheetStatus::
                where('status', config('constants.timesheet_status.AUTOMATICALLY_APPROVED'))->first()->timesheet_status_id
            ]
        );

        // Total history hours for all goal missions
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        \DB::setDefaultConnection('mysql');
        
        $response = $this->get(route('app.volunteer.history.goal-mission'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "data" => [
                    "*" => [
                        "mission_id",
                        "organisation_name",
                        "action",
                        "title"
                    ],
                ],
                "message"
            ]
        );
        
        \DB::setDefaultConnection('mysql');

        // Export total history hours for all goal missions
        $response = $this->get(route('app.volunteer.history.goal-mission.export'), ['token' => $token])
        ->seeStatusCode(200);
        
        $user->delete();        
    }

    /**
     * @test
     *
     * It should return not data found for volunteering history total hours per goal mission with export.
     *
     * @return void
     */
    public function app_volunteering_history_it_should_return_no_data_found_for_volunteering_history_total_hours_per_goal_mission_with_export()
    {
        $connection = 'tenant';

        // Create usre
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        // Total history hours for all goal missions
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        DB::setDefaultConnection('mysql');
        $response = $this->get(route('app.volunteer.history.goal-mission'), ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
                "status",
                "message"
            ]
        );
        
        DB::setDefaultConnection('mysql');
        // Export total history hours for all goal missions
        $response = $this->get(route('app.volunteer.history.goal-mission.export'), ['token' => $token])
        ->seeStatusCode(200);
        
        $user->delete();        
    }

}
