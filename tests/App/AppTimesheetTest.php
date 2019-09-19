<?php
use App\Helpers\Helpers;

class AppTimesheetTest extends TestCase
{
    /**
     * @test
     *
     * Get timesheet list
     *
     * @return void
     */
    public function it_should_return_all_timesheet_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * No data found for timesheet
     *
     * @return void
     */
    public function it_should_return_no_timesheet_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_authorization_token_for_get_timesheet()
    {
        $token = str_random(50);
        $this->get('/app/timesheet', ['token' => $token])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Add timesheet entry
     *
     * @return void
     */
    public function it_should_add_timesheet_entry()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);

        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error for invalid hours data for add timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_hours_data_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(100, 500),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/timesheet', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error for invalid minutes data for add timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_minutes_data_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 23),
            'minutes' => rand(60, 99),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/timesheet', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error for invalid day volunteer data for add timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_day_volunteer_data_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => str_random(10),
            'notes' => str_random(10),
            'hours' => rand(1, 23),
            'minutes' => rand(60, 99),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/timesheet', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error for invalid mission id data for add timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_data_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'mission_id' => rand(999999, 99999999),
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 23),
            'minutes' => rand(60, 99),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/timesheet', $params, ['token' => $token])
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
     * Update timesheet entry
     *
     * @return void
     */
    public function it_should_update_timesheet_entry()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201);

        $this->post('app/timesheet/', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            'status',
            'message',
        ]);

        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->delete();
    }
    
    /**
     * @test
     *
     * Return error for invalid hours data for update timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_hours_data_for_update_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201);

        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(100, 500),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];

        $this->post('app/timesheet/', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error for invalid minutes data for update timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_minutes_data_for_update_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201);

        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(60, 100),
            'documents[]' =>[]
        ];

        $this->post('app/timesheet/', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error for invalid day volunteer data for update timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_day_volunteer_data_for_update_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201);

        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'TEST',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];

        $this->post('app/timesheet/', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Get timesheet by timesheet id
     *
     * @return void
     */
    public function it_should_return_a_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);

        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/'.$timesheet[0]['timesheet_id'], ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "timesheet_id",
                "user_id",
                "mission_id",
                "action",
                "date_volunteered",
                "day_volunteered",
                "notes",
                "timesheet_document"
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * It should return error for get timesheet by timesheet id
     *
     * @return void
     */
    public function it_should_return_error_for_not_found_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/'.rand(100000, 500000), ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "code",
                    "message"
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Delete timesheet document
     *
     * @return void
     */
    public function it_should_delete_timesheet_document()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);

        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();
        
        $connection = 'tenant';
        $timesheetDocument = factory(\App\Models\TimesheetDocument::class)->make();
        $timesheetDocument->setConnection($connection);
        $timesheetDocument->timesheet_id = $timesheet[0]['timesheet_id'];
        $timesheetDocument->save();
        
        DB::setDefaultConnection('mysql');

        $this->delete(
            "app/timesheet/".$timesheet[0]['timesheet_id']."/document/".$timesheetDocument->timesheet_document_id,
            [],
            ['token' => $token]
        )
        ->seeStatusCode(204);

        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error for delete timesheet document
     *
     * @return void
     */
    public function it_should_return_error_for_delete_timesheet_document()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);

        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();
        DB::setDefaultConnection('mysql');

        $this->delete(
            "app/timesheet/".$timesheet[0]['timesheet_id']."/document/".rand(1000000, 5000000),
            [],
            ['token' => $token]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "code",
                    "message"
                ]
            ]
        ]);

        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error for invalid timesheet id for delete timesheet document
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_timesheet_id_for_delete_timesheet_document()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->delete(
            "app/timesheet/".rand(1000000, 5000000)."/document/".rand(1000000, 5000000),
            [],
            ['token' => $token]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "code",
                    "message"
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Submit timesheet for approval
     *
     * @return void
     */
    public function it_should_submit_timesheet_for_approval()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();
        $params = [
            'timesheet_entries' => [
                [
                    "timesheet_id" => $timesheet[0]['timesheet_id']
                ]
            ]
        ];
        
        $this->post("app/timesheet/submit", $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
       
        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return error if timesheet is already approved
     *
     * @return void
     */
    public function it_should_return_error_if_timesheet_is_already_approved()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["AUTOMATICALLY_APPROVED"]]);
        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();
        $params = [
            'timesheet_entries' => [
                [
                    "timesheet_id" => $timesheet[0]['timesheet_id']
                ]
            ]
        ];
        
        $this->post("app/timesheet/submit", $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);       
        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * It should return error for submit timesheet by timesheet id
     *
     * @return void
     */
    public function it_should_return_error_for_not_found_timesheet_id()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'timesheet_entries' => [
                [
                    "timesheet_id" => rand(100000, 500000)
                ]
            ]
        ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('/app/timesheet/submit', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "code",
                    "message"
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if timesheet data is invalid
     *
     * @return void
     */
    public function it_should_return_error_if_timesheet_data_is_invalid()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update([
            'status_id' => config("constants.timesheet_status_id")["AUTOMATICALLY_APPROVED"],
            'user_id' => \App\User::get()->random()->user_id
        ]);
        
        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->get();
        
        $params = [
            'timesheet_entries' => [
                [
                    "timesheet_id" => $timesheet[0]['timesheet_id']
                ]
            ]
        ];
        
        $this->post("app/timesheet/submit", $params, ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "code",
                    "message"
                ]
            ]
        ]);       
        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * Return all pending time requests list
     *
     * @return void
     */
    public function it_should_return_all_pending_time_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["SUBMIT_FOR_APPROVAL"]]);
        
        $this->get('/app/timesheet/time-requests', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_id",
                    "organisation_name",
                    "time",
                    "hours",
                    "title"
                ]
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return no records found for pending time requests list
     *
     * @return void
     */
    public function it_should_return_no_record_found_for_pending_time_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/time-requests', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return all pending goal requests list
     *
     * @return void
     */
    public function it_should_return_all_pending_goal_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 5),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["SUBMIT_FOR_APPROVAL"]]);
        
        $this->get('/app/timesheet/goal-requests', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_id",
                    "organisation_name",
                    "action",
                    "title"
                ]
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return no records found for pending goal requests list
     *
     * @return void
     */
    public function it_should_return_no_record_found_for_pending_goal_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/goal-requests', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Export pending time requests list
     *
     * @return void
     */
    public function it_should_export_all_pending_time_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["SUBMIT_FOR_APPROVAL"]]);
        
        $this->get('/app/timesheet/time-requests/export', ['token' => $token])
        ->seeStatusCode(200);
        $user->delete();
    }

    /**
     * @test
     *
     * Return no records found for export pending time requests list
     *
     * @return void
     */
    public function it_should_return_no_record_found_for_export_pending_time_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/time-requests/export', ['token' => $token])
        ->seeStatusCode(200);
        $user->delete();
    }

    /**
     * @test
     *
     * Export all pending goal requests list
     *
     * @return void
     */
    public function it_should_export_all_pending_goal_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 5),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["SUBMIT_FOR_APPROVAL"]]);
        
        $this->get('/app/timesheet/goal-requests/export', ['token' => $token])
        ->seeStatusCode(200);
        $user->delete();
    }

    /**
     * @test
     *
     * Return no records found for export pending goal requests list
     *
     * @return void
     */
    public function it_should_return_no_record_found_for_export_pending_goal_requests_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timesheet/goal-requests/export', ['token' => $token])
        ->seeStatusCode(200);
        $user->delete();
    }

    /**
     * @test
     *
     * Returnerror if timesheet is already approved
     *
     * @return void
     */
    public function it_should_return_error_if_timesheet_is_already_approved_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 5),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["AUTOMATICALLY_APPROVED"]]);
        
        $this->post('app/timesheet', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Returnerror if timesheet is already approved
     *
     * @return void
     */
    public function it_should_return_error_if_timesheet_is_approved_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 5),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "timesheet_id"
            ],
            'message',
        ]);
        App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])
        ->update(['status_id' => config("constants.timesheet_status_id")["APPROVED"]]);
        
        $this->post('app/timesheet', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error for invalid goal actions
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_goal_actions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(2000, 5000),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
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
     * Return error for invalid date volunteered for add timesheet
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_date_volunteered_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => '2019-04-15',
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 50),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
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
     * Return error for invalid date volunteered for add timesheet
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_date_for_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-02-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
        
        App\Models\MissionApplication::where("mission_application_id", $missionApplication[0]['mission_application_id'])
        ->update(['approval_status' => config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => '2020-04-15',
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'action' => rand(1, 50),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
        
        $this->post('app/timesheet', $params, ['token' => $token])
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
     * Return error for invalid application data for timesheet entry
     *
     * @return void
     */
    public function it_should_return_error_if_application_is_not_approved_to_add_timesheet()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
       
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => 1
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(201);
                
        $missionApplication = App\Models\MissionApplication::orderBy("mission_application_id", "DESC")->take(1)->get();
      
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'date_volunteered' => date('Y-m-d'),
            'day_volunteered' => 'HOLIDAY',
            'notes' => str_random(10),
            'hours' => rand(1, 5),
            'minutes' => rand(1, 59),
            'documents[]' =>[]
        ];
        DB::setDefaultConnection('mysql');
  
        $this->post('app/timesheet/', $params, ['token' => $token])
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
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }
}
