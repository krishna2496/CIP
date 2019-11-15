<?php
use App\Helpers\Helpers;

class AppDashboardTest extends TestCase
{
    /**
     * @test
     *
     * Get dashboard details
     *
     * @return void
     */
    public function it_should_return_dashboard_details()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/dashboard?year='.date('Y')."&month=".date('m'), ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get dashboard details
     *
     * @return void
     */
    public function it_should_return_dashboard_details_with_chart_details()
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

        $timesheet = App\Models\Timesheet::where("mission_id", $mission[0]['mission_id'])->first();
        $timesheet->update(['status_id' => config("constants.timesheet_status_id")["APPROVED"]]);

        DB::setDefaultConnection('mysql');

        $this->get('app/dashboard?mission_id='.$mission[0]['mission_id'], ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        \App\Models\Mission::whereNull('deleted_at')->delete();

    }

    /**
     * @test
     * 
     * It should list comment history on dashboard
     * @return void
     */
    public function it_should_get_list_of_comments_history_on_dashboard()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        // Creating mission
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

        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $missionId = json_decode($response->response->getContent())->data->mission_id;

        // Creating comment on created mission
        \DB::setDefaultConnection('mysql');
        $params = [
            "comment" => str_random('100'),
            "mission_id" => $missionId
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $commentResponse = $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $commentId = json_decode($commentResponse->response->getContent())->data->comment_id;

        // Update it's status
        DB::setDefaultConnection('mysql');
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];

        $this->patch('/missions/'.$missionId.'/comments/'.$commentId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        // Get list of comments for dashboard history
        \DB::setDefaultConnection('mysql');
        $this->get('app/dashboard/comments', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data' => [
                'comments' => [
                    "*" => []
                ],
                'stats'
            ],
            'message'
        ]);

        // Delete comment
        \DB::setDefaultConnection('mysql');
        $this->delete("app/dashboard/comments/$commentId", [], ['token' => $token])
        ->seeStatusCode(204);

        // Delete mission
        \DB::setDefaultConnection('mysql');
        $this->delete(
            "missions/$missionId",
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);

        // Delete user
        $user->delete();
    }

    /**
     * @test
     * 
     * It should return no comment found on dashboard comment hisotry
     * @return void
     */
    public function it_should_return_no_comment_found_for_comments_history_on_dashboard()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        \DB::setDefaultConnection('mysql');
        $this->get('app/dashboard/comments', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message'
        ]);

        // Delete user
        $user->delete();
    }

    /**
     * @test
     * 
     * It should delete comment from comments history on dashboard
     * @return void
     */
    public function it_should_delete_comment_from_comments_history_on_dashboard()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        // Creating mission
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

        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $missionId = json_decode($response->response->getContent())->data->mission_id;

        // Creating comment on created mission
        \DB::setDefaultConnection('mysql');
        $params = [
            "comment" => str_random('100'),
            "mission_id" => $missionId
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $commentResponse = $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $commentId = json_decode($commentResponse->response->getContent())->data->comment_id;

        // Update it's status
        DB::setDefaultConnection('mysql');
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];

        $this->patch('/missions/'.$missionId.'/comments/'.$commentId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        // Delete comment
        \DB::setDefaultConnection('mysql');
        $this->delete("app/dashboard/comments/$commentId", [], ['token' => $token])
        ->seeStatusCode(204);

        // Delete mission
        \DB::setDefaultConnection('mysql');
        $this->delete(
            "missions/$missionId",
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);

        // Delete user
        $user->delete();
    }

    /**
     * @test
     * 
     * It should return error, comment not found on delete comment from comments history on dashboard
     * @return void
     */
    public function it_should_return_error_comment_not_foun_on_delete_comment_from_comments_history_on_dashboard()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $commentId = rand(800000000,80000000000);

        // Delete comment
        $this->delete("app/dashboard/comments/$commentId", [], ['token' => $token])
        ->seeStatusCode(404);

        // Delete user
        $user->delete();
    }

    /**
     * @test
     * 
     * It should export user's comments
     * @return void
     */
    public function it_should_export_user_comments()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        // Creating mission
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

        $response = $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $missionId = json_decode($response->response->getContent())->data->mission_id;

        // Creating comment on created mission
        \DB::setDefaultConnection('mysql');
        $params = [
            "comment" => str_random('100'),
            "mission_id" => $missionId
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $commentResponse = $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $commentId = json_decode($commentResponse->response->getContent())->data->comment_id;

        // Update it's status
        DB::setDefaultConnection('mysql');
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];

        $this->patch('/missions/'.$missionId.'/comments/'.$commentId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        // Export comments
        \DB::setDefaultConnection('mysql');
        $this->get('app/dashboard/comments/export', ['token' => $token])
        ->seeStatusCode(200);

        // Delete comment
        \DB::setDefaultConnection('mysql');
        $this->delete("app/dashboard/comments/$commentId", [], ['token' => $token])
        ->seeStatusCode(204);

        // Delete mission
        \DB::setDefaultConnection('mysql');
        $this->delete(
            "missions/$missionId",
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);

        // Delete user
        $user->delete();
    }

    /**
     * @test
     * 
     * It should export user's comments
     * @return void
     */
    public function it_should_return_no_comment_found_on_export_user_comments()
    {
        // Creating user
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        // Export comments
        \DB::setDefaultConnection('mysql');
        $response = $this->get('app/dashboard/comments/export', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status'
        ]);

        // Delete user
        $user->delete();
    }
}
