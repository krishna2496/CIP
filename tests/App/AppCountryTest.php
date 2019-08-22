<?php
use App\Helpers\Helpers;

class AppCountryTest extends TestCase
{
    /**
     * @test
     *
     * Get country list
     *
     * @return void
     */
    public function it_should_return_all_country_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/country', ['token' => $token])
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
     * No data found for Country 
     *
     * @return void
     */
    public function it_should_return_no_country_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/country', ['token' => $token])
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
    public function it_should_return_error_for_invalid_authorization_token_for_get_country()
    {
        $token = str_random(50);
        $this->get('/app/country', ['token' => $token])
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
