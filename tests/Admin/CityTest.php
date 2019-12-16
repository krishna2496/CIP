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

    /**
     * @test
     *
     * Update city api
     *
     * @return void
     */
    public function it_should_update_city()
    {        
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->save();
        $city->country_id = $countryId;
        $city->update();

        $params = [
            "country_id"=> $countryId,
            "translations"=>[ 
                [ 
                    "lang"=>"en",
                    "name"=>str_random(10)
                ]
            ]
        ];

        $this->patch("cities/".$city->city_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
        ]);
        App\Models\Country::where('country_id', $countryId)->delete();
        App\Models\City::where('city_id', $city->city_id)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid for update city api
     *
     * @return void
     */
    public function it_should_return_error_if_data_is_invalid_for_update_city()
    {        
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->save();
        $city->country_id = $countryId;
        $city->update();

        $params = [
                "country_id" => "",
                "translations" => [ 
                   [ 
                      "lang" => "test",
                      "name" => ""
                   ]
                ]
        ];

        $this->patch("cities/".$city->city_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
        ]);
        App\Models\Country::where('country_id', $countryId)->delete();
        App\Models\City::where('city_id', $city->city_id)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid for update city api
     *
     * @return void
     */
    public function it_should_return_error_if_id_is_invalid_for_update_city()
    { 
        $this->patch("cities/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Delete city api
     *
     * @return void
     */
    public function it_should_delete_city()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->save();
        $city->country_id = $countryId;
        $city->update();

        $this->delete("cities/".$city->city_id, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        App\Models\Country::where('country_id', $countryId)->delete();
    }

    /**
     * @test
     *
     * Return error for delete city api
     *
     * @return void
     */
    public function it_should_return_error_for_delete_city()
    {
        $this->delete("cities/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
}
