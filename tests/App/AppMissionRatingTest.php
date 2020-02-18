<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppMissionRatingTest extends TestCase
{   
    /**
     * @test
     *
     * It should update mission rating
     *
     * @return void
     */
    public function it_should_update_mission_rating()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
               'country_code' => $countryDetail->ISO
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
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->update(['approval_status' => 'AUTOMATICALLY_APPROVED']);

        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'rating' => rand(1, 5)
        ];

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        DB::setDefaultConnection('mysql');
        $this->post('app/mission/rating', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\MissionRating::where(['user_id' => $user->user_id, 'mission_id' => $mission[0]['mission_id']])->delete();
        $user->delete();        
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_store_rating()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $params = [
                'mission_id' => rand(1000000, 2000000),
                'rating' => rand(1, 5)
            ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
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
     * It should return error for invalid mission rating
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_minimum_rating()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
                'mission_id' => $mission->mission_id,
                'rating' => 0.2
            ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
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
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission rating
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_maximum_rating()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
                'mission_id' => $mission->mission_id,
                'rating' => 5.5
            ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
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
        $mission->delete();
    }

    /**
     * @test
     *
     * User can apply for a mission
     *
     * @return void
     */
    public function it_should_add_rating_to_a_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
               'country_code' => $countryDetail->ISO
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
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->update(['approval_status' => 'AUTOMATICALLY_APPROVED']);

        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'rating' => rand(1, 5)
        ];

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(201)
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
     * It should return error on add mission rating
     *
     * @return void
     */
    public function it_should_add_or_update_mission_rating()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
               'country_code' => $countryDetail->ISO
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
        
        $params = [
            'mission_id' => $mission[0]['mission_id'],
            'rating' => rand(1, 5)
        ];

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(422);

        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->update(['approval_status' => 'AUTOMATICALLY_APPROVED']);

        // It should add mission rating
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/mission/rating', $params, ['token' => $token])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        DB::setDefaultConnection('mysql');
        $this->post('app/mission/rating', $params, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\MissionRating::where(['user_id' => $user->user_id, 'mission_id' => $mission[0]['mission_id']])->delete();
        $user->delete();        
        App\Models\MissionApplication::where("mission_id", $mission[0]['mission_id'])->delete();
    }
}
