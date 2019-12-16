<?php
use Illuminate\Support\Facades\DB;

class UserTest extends TestCase
{
    public function createCityCountry()
    {
        DB::setDefaultConnection('mysql');
        $iso = str_random(2);
        $params = [
            "countries" => [
                [
                    "iso" => $iso,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        /* Add country end */
        
        DB::setDefaultConnection('mysql');

        /* Add city details start */     
        $cityName = str_random(5);   
        $params = [
            "country_id" => $countryId,
            "cities" => [ 
                [ 
                    "translations" => [ 
                        [ 
                            "lang" => "en",
                            "name" => $cityName
                        ]
                    ]
                ]         
            ]
        ];

        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $cityId = json_decode($response->response->getContent())->data->city_ids[0]->city_id;
        DB::setDefaultConnection('mysql');
        /* Add city details end */
        return array('iso' => $iso, 'city_id' => $cityId, 'country_id' => $countryId);
    } 

    public function deleteCityCountry(array $cityCountryData)
    {
        DB::setDefaultConnection('tenant');
        App\Models\Country::where('country_id', $cityCountryData['country_id'])->delete();
        App\Models\City::where('city_id', $cityCountryData['city_id'])->delete();
        DB::setDefaultConnection('mysql');
    }

    /**
     * @test
     *
     * Create user api
     *
     * @return void
     */
    public function should_create_user()
    {
        $cityCountryData = $this->createCityCountry();
        $name = str_random(10);
        $params = [
                'first_name' => $name,
                'last_name' => str_random(10),
                'email' => str_random(10).'@email.com',
                'password' => str_random(10),
                'timezone_id' => 1,
                'language_id' => 1,
                'availability_id' => 1,
                'why_i_volunteer' => str_random(10),
                'employee_id' => str_random(10),
                'department' => str_random(10),
                'city_id' => $cityCountryData['city_id'],
                'country_id' => $cityCountryData['country_id'],
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'user_id',
            ],
            'message',
            'status',
        ]);
        App\User::where("first_name", $name)->orderBy("user_id", "DESC")->take(1)->delete();        
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Get all users
     *
     * @return void
     */
    public function should_return_all_users()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $search = substr($user->first_name, 1,3);

        $this->get('users?search='.$search, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "user_id",
                    "first_name",
                    "last_name",
                    "email",
                    "avatar",
                    "timezone_id",
                    "availability_id",
                    "why_i_volunteer",
                    "employee_id",
                    "department",
                    "profile_text",
                    "linked_in_url",
                    "status",
                    "timezone" => [
                        "timezone_id",
                        "timezone",
                        "offset",
                        "status"
                    ]
                ]
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * No user found
     *
     * @return void
     */
    public function should_return_no_user_found()
    {
        $this->get(route("users"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get user by id
     *
     * @return void
     */
    public function should_return_user_by_id()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->get('users/'.$user->user_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "user_id",
                "first_name",
                "last_name",
                "email",
                "avatar",
                "timezone_id",
                "availability_id",
                "why_i_volunteer",
                "employee_id",
                "department",
                "profile_text",
                "linked_in_url",
                "status",
                "timezone" => [
                    "timezone_id",
                    "timezone",
                    "offset",
                    "status"
                ]
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * No user found by id
     *
     * @return void
     */
    public function should_return_no_user_found_by_id()
    {
        $userId = rand(1000000, 50000000);
        $this->get("users/".$userId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Update user api
     *
     * @return void
     */
    public function should_update_user()
    {
        $cityCountryData = $this->createCityCountry();
        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'email' => str_random(10).'@email.com',
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'city_id' => $cityCountryData['city_id'],
            'country_id' => $cityCountryData['country_id'],
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
        ];

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->patch("users/".$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'user_id',
            ],
            'message',
            'status',
            ]);
        $user->delete();        
        $this->deleteCityCountry($cityCountryData);
    }
    
    /**
     * @test
     *
     * Update user api with already deleted or not available user id
     * @return void
     */
    public function should_return_user_not_found_on_update()
    {
        $cityCountryData = $this->createCityCountry();
        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'email' => str_random(10).'@email.com',
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'city_id' => $cityCountryData['city_id'],
            'country_id' => $cityCountryData['country_id'],
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
        ];

        $this->patch(
            "users/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Delete user
     *
     * @return void
     */
    public function should_delete_user()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->delete(
            "users/".$user->user_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete user api with already deleted or not available user id
     * @return void
     */
    public function should_return_user_not_found_on_delete()
    {
        $this->delete(
            "users/".rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
     * Return error if data is empty
     *
     * @return void
     */
    public function should_return_error_while_data_is_empty_for_create_user()
    {
        
        $params = [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'password' => '',
                'timezone_id' => 1,
                'language_id' => 1,
                'availability_id' => 1,
                'why_i_volunteer' => '',
                'employee_id' => '',
                'department' => '',
                'city_id' => '',
                'country_id' => '',
                'profile_text' => '',
                'linked_in_url' => ''
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    }

    /**
     * @test
     *
     * Return error if email is invalid
     *
     * @return void
     */
    public function should_return_error_while_email_is_invalid_for_create_user()
    {
        $cityCountryData = $this->createCityCountry();
        $name = str_random(10);
        $params = [
                'first_name' => $name,
                'last_name' => str_random(10),
                'email' => str_random(10),
                'password' => str_random(10),
                'timezone_id' => 1,
                'language_id' => 1,
                'availability_id' => 1,
                'why_i_volunteer' => str_random(10),
                'employee_id' => str_random(10),
                'department' => str_random(10),
                'city_id' => $cityCountryData['city_id'],
                'country_id' => $cityCountryData['country_id'],
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Return error for fix length
     *
     * @return void
     */
    public function should_return_error_fix_length_validation_in_create_user()
    {
        $params = [
                'first_name' => str_random(255)
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    }

    /**
     * @test
     *
     * Update user without email
     *
     * @return void
     */
    public function should_update_user_without_email_update()
    {
        $cityCountryData = $this->createCityCountry();
        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'city_id' => $cityCountryData['city_id'],
            'country_id' => $cityCountryData['country_id'],
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
        ];

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $this->patch("users/".$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'user_id',
            ],
            'message',
            'status',
            ]);
        $user->delete();
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Get all user skills
     *
     * @return void
     */
    public function should_return_user_skills()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $this->get('users/'.$user->user_id.'/skills/', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Check if user not exist in system
     *
     * @return void
     */
    public function should_return_error_if_user_is_not_exist()
    {
        $this->get('users/'.rand(100000, 500000).'/skills/', ['Authorization' => 'Basic '
        .base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
    }
    
    /**
     * @test
     *
     * If no user skills registered
     *
     * @return void
     */
    public function should_return_no_user_skills_registered()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $this->get('users/'.$user->user_id.'/skills/', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Link skill to user
     *
     * @return void
     */
    public function should_link_skill_to_user()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'skills' => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        $this->post('users/'.$user->user_id.'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $skill->delete();
    }

    /**
     * @test
     *
     * Link skill to user validate user
     *
     * @return void
     */
    public function should_validate_user_for_link_skill_to_user()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
            'skills' => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        $this->post('users/'.rand(100000, 5000000).'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(404)
          ->seeJsonStructure([
              "errors" => [
                  [
                    "status",
                    "message"
                  ]
              ]
            ]);
        $skill->delete();
    }

    /**
     * @test
     *
     * Unlink skill from user
     *
     * @return void
     */
    public function should_unlink_skill_from_user()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'skills' => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        $this->delete('users/'.$user->user_id.'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $skill->delete();
    }

    /**
     * @test
     *
     * Unlink skill to user validate user
     *
     * @return void
     */
    public function should_validate_user_for_unlink_skill_from_user()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
            'skills' => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        $this->delete('users/'.rand(100000, 5000000).'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(404)
          ->seeJsonStructure([
              "errors" => [
                  [
                    "status",
                    "message"
                  ]
              ]
            ]);
        $skill->delete();
    }

    /**
     * @test
     *
     * Return error if email is already exist
     *
     * @return void
     */
    public function should_return_error_while_email_is_exist_for_create_user()
    {
        $cityCountryData = $this->createCityCountry();
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $name = str_random(10);
        $params = [
                'first_name' => $name,
                'last_name' => str_random(10),
                'email' => $user->email,
                'password' => str_random(10),
                'timezone_id' => 1,
                'language_id' => 1,
                'availability_id' => 1,
                'why_i_volunteer' => str_random(10),
                'employee_id' => str_random(10),
                'department' => str_random(10),
                'city_id' => $cityCountryData['city_id'],
                'country_id' => $cityCountryData['country_id'],
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Return error for invalid argument
     *
     * @return void
     */
    public function should_return_invalid_argument_error_for_get_users()
    {
        $this->get("users?order=test", ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400)
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
    }

    /**
     * @test
     *
     * Return error if language id is invalid
     *
     * @return void
     */
    public function should_return_error_while_language_id_is_invalid_for_create_user()
    {
        $cityCountryData = $this->createCityCountry();
        $name = str_random(10);
        $params = [
                'first_name' => $name,
                'last_name' => str_random(10),
                'email' => str_random(10).'@email.com',
                'password' => str_random(10),
                'timezone_id' => 1,
                'language_id' => rand(1000000, 5000000),
                'availability_id' => 1,
                'why_i_volunteer' => str_random(10),
                'employee_id' => str_random(10),
                'department' => str_random(10),
                'city_id' => $cityCountryData['city_id'],
                'country_id' => $cityCountryData['country_id'],
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Return invalid data error for update user api
     *
     * @return void
     */
    public function should_return_error_for_invalid_data_on_update_user()
    {
        $cityCountryData = $this->createCityCountry();
        $params = [
            'first_name' => '',
            'last_name' => str_random(10),
            'email' => str_random(10).'@email.com',
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'city_id' => $cityCountryData['city_id'],
            'country_id' => $cityCountryData['country_id'],
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
        ];

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->patch("users/".$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->deleteCityCountry($cityCountryData);
    }
 
        /**
     * @test
     *
     * Return invalid language id error for update user api
     *
     * @return void
     */
    public function should_return_error_for_invalid_language_id_on_update_user()
    {
        $cityCountryData = $this->createCityCountry();
        $params = [
            'last_name' => str_random(10),
            'email' => str_random(10).'@email.com',
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => rand(100000, 500000),
            'availability_id' => 1,
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'city_id' => $cityCountryData['city_id'],
            'country_id' => $cityCountryData['country_id'],
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://in.linkedin.com/in/test-test-2b52238b'
        ];

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->patch("users/".$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->deleteCityCountry($cityCountryData);
    }

    /**
     * @test
     *
     * Return error if data is invalid for link skill to user
     *
     * @return void
     */
    public function should_return_error_for_invalid_data_for_link_skill_to_user()
    {
        $connection = 'tenant';
 
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [];

        $this->post('users/'.$user->user_id.'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Return error if data is invalid for unlink skill to user
     *
     * @return void
     */
    public function should_return_error_for_invalid_data_for_unlink_skill_to_user()
    {
        $connection = 'tenant';
 
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [];

        $this->delete('users/'.$user->user_id.'/skills/', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * It should return authorization error
     *
     * @return void
     */
    public function should_return_authorization_error()
    {
        $this->get('users/', [])
          ->seeStatusCode(401);
    }

    /**
     * @test
     *
     * Returns activity logs
     *
     * @return void
     */
    public function should_return_activity_logs()
    {
        $this->get("logs?from_date=".date('Y-m-d')."&to_date=".date('Y-m-d'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        DB::setDefaultConnection('mysql');

        $this->get("logs?type=".config("constants.activity_log_types.AUTH"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        DB::setDefaultConnection('mysql');

        $this->get("logs?action=".config("constants.activity_log_actions.CREATED"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        DB::setDefaultConnection('mysql');

        $this->get("logs?user_type=".config("constants.activity_log_user_types.API"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        DB::setDefaultConnection('mysql');

        $this->get("logs?users=1", ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        DB::setDefaultConnection('mysql');

        $this->get("logs?type=test", ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }
}
