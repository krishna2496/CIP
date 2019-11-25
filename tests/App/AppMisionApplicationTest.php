<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;
use Carbon\Carbon;

class AppMisionApplicationTest extends TestCase
{
    /**
     * @test
     *
     * Return no mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found_for_apply()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
       
        $params = [
                'mission_id' => rand(1000000, 20000000),
                'availability_id' => 1,
                'motivation' => str_random(10)
            ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(422)
          ->seeJsonStructure([
              'errors' => [
                  [
                      'status',
                      'type',
                      'code',
                      'message'
                  ]
              ]
          ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if user already applied for a mission
     *
     * @return void
     */
    public function it_should_return_error_for_already_applied_to_a_mission()
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
        
        $params = [
                'mission_id' => $mission->mission_id,
                'availability_id' => 1,
                'motivation' => str_random(10)
            ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
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
        $mission->delete();
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if deadline is passed
     *
     * @return void
     */
    public function it_should_return_error_if_deadline_is_passed()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        // Add mission with passed deadline
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
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2019-07-25 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'availability_id' => 1,
                'motivation' => str_random(10)
            ];
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
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
     * Return error if no seat available
     *
     * @return void
     */
    public function it_should_return_error_if_seats_not_available()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        // Add mission with passed deadline
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
            "total_seats" => 1,
            "application_deadline" => "2020-07-25 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        App\Models\Mission::where("mission_id", $mission[0]['mission_id'])->update(["total_seats" => 0]);
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'availability_id' => 1,
                'motivation' => str_random(10)
            ];
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/application', $params, ['token' => $token])
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
        App\Models\Mission::where("mission_id", $mission[0]['mission_id'])->take(1)->delete();
    }

    /**
     * @test
     *
     * User can apply for a mission
     *
     * @return void
     */
    public function it_should_add_record_for_apply_to_a_mission()
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
          ->seeStatusCode(201)
          ->seeJsonStructure([
            'status',
            'data' => [
                "mission_application_id"
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
     * Return error if deadline is passed
     *
     * @return void
     */
    public function it_should_return_error_if_deadline_is_passed_for_goal_mission()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        // Add mission with passed deadline
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
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2019-07-25 11:40:00",
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
        $user->delete();
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }
}
