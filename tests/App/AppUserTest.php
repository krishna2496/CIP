<?php
use App\Helpers\Helpers;

class AppUserTest extends TestCase
{
    /**
     * @test
     *
     * Search user by first name
     *
     * @return void
     */
    public function it_should_search_user_by_first_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->first_name, 2), ['token' => $token])
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
     * Search user by last name
     *
     * @return void
     */
    public function it_should_search_user_by_last_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->last_name, 2), ['token' => $token])
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
     * Search user by email
     *
     * @return void
     */
    public function it_should_search_user_by_email()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.substr($user->email, 3), ['token' => $token])
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
     * Search user by email
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_param_for_search_user()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('app/search-user?search='.str_random(5), ['token' => $token])
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
     * Edit user data
     *
     * @return void
     */
    public function it_should_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $userCustomField = factory(\App\Models\UserCustomField::class)->make();
        $userCustomField->setConnection($connection);
        $userCustomField->save();
        $fieldId = $userCustomField->field_id;

        $params = [
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'timezone_id' => 1,
            'language_id' => 1,
            'availability_id' => 1,
            'why_i_volunteer' => str_random(50),
            'employee_id' => str_random(3),
            'department' => str_random(5),
            'manager_name' => str_random(5),
            'custom_fields' => [
                [
                    "field_id" => $fieldId,
                    "value" => "1"
                ]
            ]
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/', $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $userCustomField->delete();
    }

    /**
     * @test
     *
     * Return error if user data is invalid
     *
     * @return void
     */
    public function it_should_return_error_if_data_is_invalid_for_save_user_data()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
      
        $params = [
            'availability_id' => rand(1000000, 2000000)
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/user/', $params, ['token' => $token])
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
}
