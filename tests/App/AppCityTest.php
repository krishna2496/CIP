<?php
use App\Helpers\Helpers;

class AppCityTest extends TestCase
{
    /**
     * @test
     *
     * Get city list
     *
     * @return void
     */
    public function it_should_return_all_city_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/city/'.$countryId, ['token' => $token])
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
     * No data found for city 
     *
     * @return void
     */
    public function it_should_return_no_city_found_for_country_id()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/city/'.rand(1000000, 5000000), ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
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
    public function it_should_return_error_for_invalid_authorization_token_for_get_city()
    {
        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;

        $token = str_random(50);
        $this->get('/app/city/', ['token' => $token])
        ->seeStatusCode(500)
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

    /**
     * @test
     *
     * No data found for city 
     *
     * @return void
     */
    public function it_should_return_no_city_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;

        $connection = 'tenant';
        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->country_id = $countryId;
        $city->save();

        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/city/'.$countryId, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $city->delete();
    }
}
