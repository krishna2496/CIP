<?php
use App\Helpers\Helpers;

class CityTest extends TestCase
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
        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;
        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
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
        $this->get('/cities/'.rand(1000000, 5000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        DB::setDefaultConnection('mysql');

        $token = str_random(50);
        $this->get('/cities/'.$countryId, ['Authorization' => ''])
        ->seeStatusCode(401)
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
        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;
        DB::setDefaultConnection('mysql');

        $connection = 'tenant';
        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->country_id = $countryId;
        $city->save();

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $city->delete();
    }
}
