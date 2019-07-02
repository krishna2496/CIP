<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class UserTest extends TestCase
{
    /**
     * @test
     *
     * Get all users
     *
     * @return void
     */
    public function it_should_return_all_users()
    {
        $this->get(route('users'), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
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
        $this->get(route("users"), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get all users
     *
     * @return void
     */
    public function it_should_return_user_by_id()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $userId = $user->user_id;
        $this->get('users/'.$userId, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
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
        $this->get("users/".$userId, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(404);
    }

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
                'timezone_id' => rand(1, 1),
                'language_id' => rand(1, 1),
                'availability_id' => rand(1, 50),
                'why_i_volunteer' => str_random(10),
                'employee_id' => str_random(10),
                'department' => str_random(10),
                'manager_name' => str_random(10),
                'city_id' => rand(1, 1),
                'country_id' => rand(1, 1),
                'profile_text' => str_random(10),
                'linked_in_url' => 'https://www.'.str_random(10).'.com'
            ];

        $this->post("users/", $params, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'user_id',
            ],
            'message',
            'status',
        ]);
        
        User::where("first_name", $name)->orderBy("user_id", "DESC")->take(1)->delete();
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
            'timezone_id' => rand(1, 1),
            'language_id' => rand(1, 1),
            'availability_id' => rand(1, 50),
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'manager_name' => str_random(10),
            'city_id' => rand(1, 1),
            'country_id' => rand(1, 1),
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://www.'.str_random(10).'.com'
        ];

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user_id = $user->user_id;

        $this->patch("users/".$user_id, $params, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
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
            'timezone_id' => rand(1, 1),
            'language_id' => rand(1, 1),
            'availability_id' => rand(1, 50),
            'why_i_volunteer' => str_random(10),
            'employee_id' => str_random(10),
            'department' => str_random(10),
            'manager_name' => str_random(10),
            'city_id' => rand(1, 1),
            'country_id' => rand(1, 1),
            'profile_text' => str_random(10),
            'linked_in_url' => 'https://www.'.str_random(10).'.com'
        ];

        $this->patch(
            "users/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
        )
        ->seeStatusCode(404);
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
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
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
            ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ=']
        )
        ->seeStatusCode(404);
    }
}
