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
        DB::setDefaultConnection('mysql');

        $token = Helpers::getTestUserToken($user->user_id);
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
        $token = Helpers::getTestUserToken($user->user_id);
        
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
        $token = Helpers::getTestUserToken($user->user_id);
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        DB::setDefaultConnection('mysql');
     
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $params = [
                'mission_id' => $missionId
            ];

        $token = Helpers::getTestUserToken($user->user_id);
        $this->post('app/mission/favourite', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        App\Models\FavouriteMission::where('user_id', $user->user_id)->delete();
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        DB::setDefaultConnection('mysql');
     
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $params = [
                'mission_id' => $missionId
            ];

        $token = Helpers::getTestUserToken($user->user_id);
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getTestUserToken($userId);
        $this->get('app/mission/'.$missionId, ['token' => $token])
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
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getTestUserToken($userId);
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getTestUserToken($userId);
        $this->get('/app/related-missions/'.$missionId, ['token' => $token])
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
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getTestUserToken($userId);
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
        DB::setDefaultConnection('tenant');
        $missionId = App\Models\Mission::get()->random()->mission_id;
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');

        $token = Helpers::getTestUserToken($userId);
        $this->get('app/mission/'.$missionId.'/volunteers', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
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
        DB::setDefaultConnection('tenant');
        $userId = App\User::get()->random()->user_id;
        DB::setDefaultConnection('mysql');
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getTestUserToken($userId);
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
    }
}
