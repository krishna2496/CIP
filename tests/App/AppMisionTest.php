<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppMissionTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_app_missions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get(route('app.missions'), ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "meta_data" => [
                "filters" => [
                    "search"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * No mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found_for_app()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $this->get(route('app.missions'), ['token' => $token])
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
     * Show error invalid credentials
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_token()
    {
        $this->get(route('app.missions'), ['token' => str_random(100)])
        ->seeStatusCode(400);
    }

    /**
     * @test
     *
     * It should validate data for add mission to favourite
     *
     * @return void
     */
    public function it_should_validate_request_for_add_mission_to_favourite()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::setDefaultConnection('mysql');

        $params = [
                'mission_id' => rand(1000000, 2000000)
            ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/favourite', $params, ['token' => $token])
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
    }

    /**
     * @test
     *
     * It should add mission to favourite
     *
     * @return void
     */
    public function it_should_add_mission_to_favourite()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $params = [
                'mission_id' => $mission->mission_id
            ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/favourite', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\FavouriteMission::where('user_id', $user->user_id)->delete();
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should remove mission from favourite
     *
     * @return void
     */
    public function it_should_remove_mission_from_favourite()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        
        $params = [
                'mission_id' => $mission->mission_id
            ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        //Code for add mission to favourite
        $this->post('app/mission/favourite', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        // change database connection to master
        DB::setDefaultConnection('mysql');
        $this->post('app/mission/favourite', $params, ['token' => $token])
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
     * Get mission detail by mission id
     *
     * @return void
     */
    public function it_should_return_app_mission_detail_by_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/mission/'.$mission->mission_id, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_id",
                    "theme_id",
                    "city_id",
                    "country_id",
                    "start_date",
                    "end_date",
                    "total_seats",
                    "mission_type",
                    "publication_status",
                    "organisation_id",
                    "organisation_name",
                    "user_application_count",
                    "mission_application_count",
                    "favourite_mission_count",
                    "mission_rating_count",
                    "mission_rating_total_volunteers",
                    "user_application_status",
                    "rating",
                    "is_favourite",
                    "seats_left",
                    "default_media_type",
                    "default_media_path",
                    "title",
                    "short_description",
                    "set_view_detail",
                    "city_name",
                    "mission_theme"=> [
                        "mission_theme_id",
                        "theme_name",
                        "translations"
                    ],
                    "mission_document"=> []
                ]
            ],
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
    public function it_should_return_error_for_invalid_mission_id_for_get_mission_details()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/mission/'.$missionId, ['token' => $token])
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
    }

    /**
     * @test
     *
     * Get mission detail by mission id
     *
     * @return void
     */
    public function it_should_return_related_mission_by_id()
    {
        $connection = 'tenant';
        $missionRelated = factory(\App\Models\Mission::class)->make();
        $missionRelated->setConnection($connection);
        $missionRelated->save();

        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/related-missions/'.$mission->mission_id, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_id",
                    "theme_id",
                    "city_id",
                    "country_id",
                    "start_date",
                    "end_date",
                    "total_seats",
                    "mission_type",
                    "publication_status",
                    "organisation_id",
                    "organisation_name",
                    "user_application_count",
                    "mission_application_count",
                    "favourite_mission_count",
                    "mission_rating_count",
                    "user_application_status",
                    "rating",
                    "is_favourite",
                    "seats_left",
                    "default_media_type",
                    "default_media_path",
                    "title",
                    "short_description",
                    "set_view_detail",
                    "city_name",
                    "mission_theme"=> [
                        "mission_theme_id",
                        "theme_name",
                        "translations"
                    ]
                ],
            ],
            "message"
        ]);
        $user->delete();
        $mission->delete();
        $missionRelated->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_to_get_related_mission()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/related-missions/'.$missionId, ['token' => $token])
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
    }

    /**
     * @test
     *
     * Get mission volunteers by mission id
     *
     * @return void
     */
    public function it_should_return_app_mission_volunteers_by_mission_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/mission/'.$mission->mission_id.'/volunteers', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $mission->delete();
        $user->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_get_volunteers()
    {
        $connection = 'tenant';        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/mission/'.$missionId.'/volunteers', ['token' => $token])
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
    }

    /**
     * @test
     *
     * It should validate data for add mission to favourite
     *
     * @return void
     */
    public function it_should_validate_mission_id_for_add_mission_to_favourite()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::setDefaultConnection('mysql');

        $params = [
                'mission_id' => "test"
            ];
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/favourite', $params, ['token' => $token])
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
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_top_recommended_app_missions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/missions?explore_mission_type=recommended-missions', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "meta_data" => [
                "filters" => [
                    "search"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_blank_app_missions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/missions?explore_mission_type=recommended-missions1', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "meta_data" => [
                "filters" => [
                    "search"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Explore mission
     *
     * @return void
     */
    public function it_should_return_explore_missions()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/explore-mission', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_filter_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/filter-data?country_id='.$mission->country_id.'&city_id='.$mission->city_id, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data"
        ]);
        $user->delete();
    }
}
