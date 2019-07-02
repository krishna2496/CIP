<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Models\TenantOption;

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

        $this->post('login', $params, [])
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

        $this->post('login', $params, [])
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

        $this->post('request_password_reset', $params, [])
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
            'email' => str_random(10).'@gmail.com',
        ];

        $this->post('request_password_reset', $params, [])
          ->seeStatusCode(403);
    }
}
