<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class AppAuthTest extends TestCase
{    
    /**
     * @test
     *
     * Get user details after login
     *
     * @return void
     */
    public function it_should_login_user()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $password = str_random(5);
        $user->password = $password;
        $user->update();

        $params = [
            'email' => $user->email,
            'password' => $password,
        ];

        $this->post('app/login', $params, [])
          ->seeStatusCode(200)
          ->seeJsonStructure(
              [
                "status",
                "data" =>  [
                    "token",
                    "user_id",
                    "first_name",
                    "last_name",
                    "avatar"
                ],
                "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     *
     * Show error if data is invalid
     *
     * @return void
     */
    public function it_should_show_error_if_credentials_invalid()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'email' => $user->email,
            'password' => $user->password,
        ];

        $this->post('app/login', $params, [])
          ->seeStatusCode(422);
        $user->delete();
    }

    /**
     * @test
     *
     * Request password reset
     *
     * @return void
     */
    public function it_should_sent_request_for_reset_password()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'email' => $user->email,
        ];

        $this->post('app/request-password-reset', $params, [])
          ->seeStatusCode(200)
          ->seeJsonStructure(
              [
                "status",
                "message"
            ]
        );
        $user->delete();
    }

    /**
     * @test
     *
     * Show error if incorrect email
     *
     * @return void
     */
    public function it_should_show_error_for_incorrect_email()
    {
        $params = [
            'email' => str_random(10),
        ];
        $this->post('app/request-password-reset', $params, [])
          ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Show error if incorrect email
     *
     * @return void
     */
    public function it_should_show_error_for_invalid_reset_password_token()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $params = [
            'email' => $user->email,
            'reset_password_token' => '',
            'password' => "12345678",
            'password_confirmation' =>"12345678",
        ];

        $this->put('app/password-reset', $params, [])
          ->seeStatusCode(422);
        $user->delete();
    }

    /**
     * @test
     *
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function it_should_reset_password()
    {
        Notification::fake();
        $token = '';
        
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->post('app/request-password-reset', ['email' => $user->email])
            ->seeStatusCode(200);

        Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\ResetPassword::class,
            function ($notification, $channels) use (&$token) {
                $token = $notification->token;

                return true;
            }
        );

        DB::setDefaultConnection('mysql');

        $response = $this->put('app/password-reset', [
            'reset_password_token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $user->delete();
    }

    /**
     * @test
     *
     * Show error if email is invalid
     *
     * @return void
     */
    public function it_should_show_error_if_email_is_invalid()
    {
        $params = [
            'email' => 'test@gmail.com',
            'password' => 'test',
        ];
        $this->post('app/login', $params, [])
          ->seeStatusCode(403);
    }
}
