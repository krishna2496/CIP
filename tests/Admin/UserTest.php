<?php

use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\Mission;
use App\Models\MissionApplication;
use App\Models\Timesheet;
use App\Models\MissionLanguage;

class UserTest extends TestCase
{
    /**
     * @test
     *
     * Create user api
     *
     * @return void
     */
    public function it_should_create_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;        
        \DB::setDefaultConnection('mysql');
        
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
                'city_id' => $cityId,
                'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Get all users
     *
     * @return void
     */
    public function it_should_return_all_users()
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
    public function it_should_return_no_user_found()
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
    public function it_should_return_user_by_id()
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
    public function it_should_return_no_user_found_by_id()
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
    public function it_should_update_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
        
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
            'city_id' => $cityId,
            'country_id' => $countryDetail->country_id,
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
        
    }
    
    /**
     * @test
     *
     * Update user api with already deleted or not available user id
     * @return void
     */
    public function it_should_return_user_not_found_on_update()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
        
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
            'city_id' => $cityId,
            'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Delete user
     *
     * @return void
     */
    public function it_should_delete_user()
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
    public function it_should_return_user_not_found_on_delete()
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
    public function it_should_return_error_while_data_is_empty_for_create_user()
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
    public function it_should_return_error_while_email_is_invalid_for_create_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
        
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
                'city_id' => $cityId,
                'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Return error for fix length
     *
     * @return void
     */
    public function it_should_return_error_fix_length_validation_in_create_user()
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
    public function it_should_update_user_without_email_update()
    {
       
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
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
            'city_id' => $cityId,
            'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Get all user skills
     *
     * @return void
     */
    public function it_should_return_user_skills()
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
    public function it_should_return_error_if_user_is_not_exist()
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
    public function it_should_return_no_user_skills_registered()
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
    public function it_should_link_skill_to_user()
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
    public function it_should_validate_user_for_link_skill_to_user()
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
    public function it_should_unlink_skill_from_user()
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
    public function it_should_validate_user_for_unlink_skill_from_user()
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
    public function it_should_return_error_while_email_is_exist_for_create_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
        
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
                'city_id' => $cityId,
                'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Return error for invalid argument
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_for_get_users()
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
    public function it_should_return_error_while_language_id_is_invalid_for_create_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
            
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
                'city_id' => $cityId,
                'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Return invalid data error for update user api
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_data_on_update_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
            
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
            'city_id' => $cityId,
            'country_id' => $countryDetail->country_id,
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
        
    }
 
        /**
     * @test
     *
     * Return invalid language id error for update user api
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_language_id_on_update_user()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        \DB::setDefaultConnection('mysql');
            
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
            'city_id' => $cityId,
            'country_id' => $countryDetail->country_id,
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
        
    }

    /**
     * @test
     *
     * Return error if data is invalid for link skill to user
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_data_for_link_skill_to_user()
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
    public function it_should_return_error_for_invalid_data_for_unlink_skill_to_user()
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
    public function it_should_return_authorization_error()
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
    public function it_should_return_activity_logs()
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

    /**
     * @test
     *
     * it should return user with specified email
     *
     * @return void
     */

    public function it_should_return_user_with_given_email()
    {
        $connection = 'tenant';

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        // Create two skill record.

        $userIds = [];
        $userCollection = [];
        $users = factory(\App\User::class, 2)->make();

        foreach ($users as $key => $user) {
            $user->setConnection($connection);
            $user->save();
            $userIds[] = $user->user_id;
            $userCollection[] = $user->toArray();
        }

        // Get the first created user
        $userEmail = $userCollection[0]['email'];

        $response = $this->get('users?email='.$userEmail, $authorization)
          ->seeStatusCode(200)
          ->seeJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'user_id',
                    'first_name',
                    'last_name',
                    'email',
                    'avatar',
                    'timezone_id',
                    'availability_id',
                    'why_i_volunteer',
                    'employee_id',
                    'department',
                    'profile_text',
                    'linked_in_url',
                    'status',
                    'timezone' => [
                        'timezone_id',
                        'timezone',
                        'offset',
                        'status'
                    ]
                ]
            ],
            'message'
        ]);

        $result = json_decode($response->response->getContent());

        $this->assertSame($result->pagination->total, 1);

        foreach ($result->data as $key => $data) {
            $this->assertSame($data->email, $userEmail);
        }

        // Forced Delete all created user. It will not softdelete user

        DB::setDefaultConnection('tenant');

        \App\User::whereIn('user_id', $userIds)->forceDelete();

    }

    /**
     * @test
     *
     * it should return correct user timesheet summary
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheet_summary()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount)->seeJsonStructure(
          [
              'status',
              'data' => [
                  'total_timesheet_time',
                  'total_timesheet_action',
                  'total_timesheet'
              ],
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 200);
      $this->assertSame($result->data->total_timesheet_time, '03:03:03');
      $this->assertSame($result->data->total_timesheet_action, null);
      $this->assertSame($result->data->total_timesheet, $timesheetCount);
      $this->assertSame($result->message, 'User timesheet summarized successfully');

    }

    /**
     * @test
     *
     * it should return correct user timesheet summary with specified valid status
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheet_summary_with_specified_valid_status()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount, 'timesheet-summary', 'WORKDAY')->seeJsonStructure(
          [
              'status',
              'data' => [
                  'total_timesheet_time',
                  'total_timesheet_action',
                  'total_timesheet'
              ],
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 200);
      $this->assertSame($result->data->total_timesheet_time, '03:03:03');
      $this->assertSame($result->data->total_timesheet_action, null);
      $this->assertSame($result->data->total_timesheet, $timesheetCount);
      $this->assertSame($result->message, 'User timesheet summarized successfully');

    }

    /**
     * @test
     *
     * it should return correct user timesheet summary with specified invalid status
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheet_summary_with_specified_invalid_status()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount, 'timesheet-summary', 'HOLIDAY')->seeJsonStructure(
          [
              'status',
              'data' => [
                  'total_timesheet_time',
                  'total_timesheet_action',
                  'total_timesheet'
              ],
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 200);
      $this->assertSame($result->data->total_timesheet_time, null);
      $this->assertSame($result->data->total_timesheet_action, null);
      $this->assertSame($result->data->total_timesheet, 0);
      $this->assertSame($result->message, 'User timesheet summarized successfully');

    }

    /**
     * @test
     *
     * it should return correct user timesheets per mission
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheets_per_mission()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount, 'timesheet')->seeJsonStructure(
          [
              'status',
              'data' => [
                '*' => [
                  'mission_id',
                  'mission_type',
                  'mission_title',
                  'total_timesheet_time',
                  'total_timesheet_action',
                  'total_timesheet'
                ]
              ],
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 200);
      $this->assertSame($result->data[0]->mission_type, 'GOAL');
      $this->assertSame($result->data[0]->mission_title, 'mission title');
      $this->assertSame($result->data[0]->total_timesheet_time, '03:03:03');
      $this->assertSame($result->data[0]->total_timesheet_action, null);
      $this->assertSame($result->data[0]->total_timesheet, $timesheetCount);
      $this->assertSame($result->message, 'User timesheet listed successfully');

    }

    /**
     * @test
     *
     * it should return correct user timesheets per mission with specified valid status
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheets_per_mission_with_specified_valid_status()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount, 'timesheet', 'WORKDAY')->seeJsonStructure(
          [
              'status',
              'data' => [
                '*' => [
                  'mission_id',
                  'mission_type',
                  'mission_title',
                  'total_timesheet_time',
                  'total_timesheet_action',
                  'total_timesheet'
                ]
              ],
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 200);
      $this->assertSame($result->data[0]->mission_type, 'GOAL');
      $this->assertSame($result->data[0]->mission_title, 'mission title');
      $this->assertSame($result->data[0]->total_timesheet_time, '03:03:03');
      $this->assertSame($result->data[0]->total_timesheet_action, null);
      $this->assertSame($result->data[0]->total_timesheet, $timesheetCount);
      $this->assertSame($result->message, 'User timesheet listed successfully');

    }

    /**
     * @test
     *
     * it should return correct user timesheets per mission with specified invalid status
     *
     * @return void
     */
    public function it_should_return_correct_user_timesheets_per_mission_with_specified_invalid_status()
    {

      $timesheetCount = 3;
      $response = $this->mockTimesheetCreation($timesheetCount, 'timesheet', 'SAMPLE')->seeJsonStructure(
          [
              'status',
              'message'
          ]
      );
      $result = json_decode($response->response->getContent());

      $this->assertSame($result->status, 404);
      $this->assertSame($result->message, 'User timesheets not found');

    }

    private function mockTimesheetCreation($timesheetCount, $action = 'timesheet-summary', $status = null)
    {
        $connection = 'tenant';
        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        $user = factory(User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Create a mock mission

        $mission = factory(Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        // Create a mock mission application

        $missionApplication = factory(MissionApplication::class)->make([
            'user_id' => $user->user_id,
            'mission_id' => $mission->mission_id
        ]);
        $missionApplication->setConnection($connection);
        $missionApplication->save();

        // Create a mock mission language

        if ($action === 'timesheet') {
          $missionLanguage = factory(MissionLanguage::class)->make([
              'mission_id' => $mission->mission_id
          ]);
          $missionLanguage->setConnection($connection);
          $missionLanguage->save();
        }

        // Create a mock timesheet

        $timesheetId = [];
        $timesheets = factory(Timesheet::class, $timesheetCount)->make([
            'user_id' => $user->user_id,
            'mission_id' => $mission->mission_id,
        ]);

        foreach ($timesheets as $key => $timesheet) {
            $timesheet->setConnection($connection);
            $timesheet->save();
            $timesheetId[] = $timesheet->timesheet_id;
        }

        $response = $this->get("users/$user->user_id/$action?day_volunteered=$status", $authorization);

        // Forced Delete all created user, mission, mission_application and timesheet. It will not softdelete mission

        DB::setDefaultConnection($connection);

        User::where('user_id', $user->user_id)->forceDelete();
        Mission::where('mission_id', $mission->mission_id)->forceDelete();
        MissionApplication::where('mission_application_id', $missionApplication->mission_application_id)->forceDelete();
        Timesheet::whereIn('timesheet_id', $timesheetId)->forceDelete();
        if ($action === 'timesheet') {
          MissionLanguage::where('mission_language_id', $missionLanguage->mission_language_id)->forceDelete();
        }

        // Return $response

        return $response;

    }

}
