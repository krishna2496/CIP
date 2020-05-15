<?php
use App\Helpers\Helpers;

class AppTimezoneTest extends TestCase
{
    /**
     * @test
     *
     * Get timezone list
     *
     * @return void
     */
    public function it_should_return_all_timezone_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timezone', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "data" => [
                "*" => []
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * No data found for timezone 
     *
     * @return void
     */
    public function it_should_return_no_timezone_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/timezone', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     * 
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_authorization_token_for_get_timezone()
    {
        $token = str_random(50);
        $this->get('/app/timezone', ['token' => $token])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
        ]);
    }
}
