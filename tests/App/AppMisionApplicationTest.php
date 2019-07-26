<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\DB;
use App\Models\Mission;
use App\Helpers\Helpers;

class AppMisionApplicationTest extends TestCase
{
    /**
     * @test
     *
     * No mission found
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
                'mission_id' => rand(1000000, 20000000)
            ];
        $token = Helpers::getTestUserToken($user->user_id);
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(422);
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
        DB::setDefaultConnection('tenant');
        $missionApplication = App\Models\MissionApplication::get()->random();
        $missionId = $missionApplication->mission_id;
        $userId = $missionApplication->user_id;
        DB::setDefaultConnection('mysql');
        
        $params = [
                'mission_id' => $missionId
            ];
        $token = Helpers::getTestUserToken($userId);
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(422);
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
                "organisation_id" => rand(1, 1),
                "organisation_name" => str_random(10)
            ],
            "location" => [
                "city_id" => rand(1, 1),
                "country_code" => "IN"
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
                    "media_name" => "TatvaSoft-Software-Development-Company.png",
                    "media_type" => "png",
                    "media_path" => "http://web8.anasource.com/team4/cip-api-swagger/group-img1.png",
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2019-10-15 10:40:00",
            "mission_type" => "TIME",
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 1000),
            "application_deadline" => "2019-07-25 11:40:00",
            "publication_status" => "APPROVED",
            "theme_id" => rand(1, 1)
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = Mission::orderBy("mission_id", "DESC")->take(1)->get();
        
        $params = [
                'mission_id' => $mission[0]['mission_id']
            ];
        DB::setDefaultConnection('mysql');
        $token = Helpers::getTestUserToken($user->user_id);
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(422);
        $user->delete();
        Mission::orderBy("mission_id", "DESC")->take(1)->delete();
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
                "organisation_id" => rand(1, 1),
                "organisation_name" => str_random(10)
            ],
            "location" => [
                "city_id" => rand(1, 1),
                "country_code" => "IN"
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
                    "media_name" => "TatvaSoft-Software-Development-Company.png",
                    "media_type" => "png",
                    "media_path" => "http://web8.anasource.com/team4/cip-api-swagger/group-img1.png",
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2019-10-15 10:40:00",
            "mission_type" => "GOAL",
            "goal_objective" => rand(1, 1000),
            "total_seats" => 0,
            "application_deadline" => "2019-07-25 11:40:00",
            "publication_status" => "APPROVED",
            "theme_id" => rand(1, 1)
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = Mission::orderBy("mission_id", "DESC")->take(1)->get();
        
        $params = [
                'mission_id' => $mission[0]['mission_id']
            ];
        DB::setDefaultConnection('mysql');
        $token = Helpers::getTestUserToken($user->user_id);
        $this->post('app/mission/application', $params, ['token' => $token])
          ->seeStatusCode(422);
        $user->delete();
        Mission::orderBy("mission_id", "DESC")->take(1)->delete();
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
                "organisation_id" => rand(1, 1),
                "organisation_name" => str_random(10)
            ],
            "location" => [
                "city_id" => rand(1, 1),
                "country_code" => "IN"
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
                    "media_name" => "TatvaSoft-Software-Development-Company.png",
                    "media_type" => "png",
                    "media_path" => "http://web8.anasource.com/team4/cip-api-swagger/group-img1.png",
                    "default" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2020-10-15 10:40:00",
            "mission_type" => "TIME",
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1,10),
            "application_deadline" => "2020-10-15 10:40:00",
            "publication_status" => "APPROVED",
            "theme_id" => rand(1, 1)
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = Mission::orderBy("mission_id", "DESC")->take(1)->get();
        
        $params = [
                'mission_id' => $mission[0]['mission_id'],
                'motivation' => str_random(10),
                'availability_id' => rand(1,1)
            ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getTestUserToken($user->user_id);
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
        Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }
    

}
