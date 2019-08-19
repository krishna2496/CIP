<?php
use Illuminate\Support\Facades\DB;

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
                'manager_name' => str_random(10),
                'city_id' => 1,
                'country_id' => 233,
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://www.'.str_random(10).'.com'
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

        $this->get(route('users'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                    "manager_name",
                    "city_id",
                    "country_id",
                    "profile_text",
                    "linked_in_url",
                    "status",
                    "city" => [
                        "city_id",
                        "name",
                        "country_id"
                    ],
                    "country" => [
                        "country_id",
                        "name",
                        "ISO"
                    ],
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
                "manager_name",
                "city_id",
                "country_id",
                "profile_text",
                "linked_in_url",
                "status",
                "city" => [
                    "city_id",
                    "name",
                    "country_id"
                ],
                "country" => [
                    "country_id",
                    "name",
                    "ISO"
                ],
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
            'manager_name' => str_random(10),
            'city_id' => 1,
            'country_id' => 233,
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://www.'.str_random(10).'.com'
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
        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'email' => str_random(10).'@email.com',
            'password' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => rand(1, 50),
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'manager_name' => str_random(10),
            'city_id' => 1,
            'country_id' => 233,
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://www.'.str_random(10).'.com'
        ];

        $this->patch(
            "users/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
                'manager_name' => '',
                'city_id' => 1,
                'country_id' => 233,
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
                'manager_name' => str_random(10),
                'city_id' => 1,
                'country_id' => 233,
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://www.'.str_random(10).'.com'
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
            'manager_name' => str_random(10),
            'city_id' => 1,
            'country_id' => 233,
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://www.'.str_random(10).'.com'
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
        
        $this->get('user/skills/'.$user->user_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->get('user/skills/'.rand(100000, 500000), ['Authorization' => 'Basic '
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
        
        $this->get('user/skills/'.$user->user_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $this->post('user/skills/'.$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $this->post('user/skills/'.rand(100000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $this->delete('user/skills/'.$user->user_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $this->delete('user/skills/'.rand(100000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                'manager_name' => str_random(10),
                'city_id' => 1,
                'country_id' => 233,
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://www.'.str_random(10).'.com'
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
}
