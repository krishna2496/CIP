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
     * Change password
     *
     * @return void
     */
    public function it_should_change_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user->password = "123456789";
        $user->update();

        $params = [
            'old_password' => "123456789",
            'password' => "12345678",
            'confirm_password' => "12345678"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure(
            [
            "status",
            "data" =>[
                "token"
            ],
            "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     *
     * Show error if incorrect old password
     *
     * @return void
     */
    public function it_should_show_error_for_incorrect_old_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'old_password' => "test",
            'password' => "12345678",
            'confirm_password' => "12345678"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
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
     * Show error if password and confirm password does not matched
     *
     * @return void
     */
    public function it_should_show_error_for_new_password_does_not_matched()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user->password = "123456789";
        $user->update();

        $params = [
            'old_password' => "123456789",
            'password' => "12345678",
            'confirm_password' => "1234567800"
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
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
     * Show error if required fields are empty
     *
     * @return void
     */
    public function it_should_show_error_if_required_fields_are_empty_for_change_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'old_password' => "",
            'password' => "",
            'confirm_password' => ""
        ];
        $token = Helpers::getJwtToken($user->user_id);
        $this->patch('app/change-password', $params, ['token' => $token])
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
}
