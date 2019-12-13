<?php

class CountryTest extends TestCase
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
        $this->get('/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "*" => []
            ],
            "message"
        ]);
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
        $this->get('/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
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
        $this->get('/app/country', ['Authorization' => ''])
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
     * Update country api
     *
     * @return void
     */
    public function it_should_update_country()
    {
        $iso = str_random(3);

        $params = [
            "iso"=>$iso,
            "translations"=>[
                [
                    "lang"=>"en",
                    "name"=>str_random(10)
                ]
            ]
        ];

        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $this->patch("countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
        ]);
        App\Models\Country::where('ISO', $iso)->delete();
    }

    /**
     * @test
     *
     * Update country api
     *
     * @return void
     */
    public function it_should_return_error_if_iso_is_invalid_for_update_country()
    {
        $params = [
            "iso"=>"",
            "translations"=>[
                [
                    "lang"=>"en",
                    "name"=>str_random(10)
                ]
            ]
        ];

        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $this->patch("countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $country->delete();
    }

    /**
     * @test
     *
     * Update country api
     *
     * @return void
     */
    public function it_should_return_error_if_data_is_invalid_for_update_country()
    {
        $params = [
            "iso"=>"",
            "translations"=>[
                [
                    "lang"=>"test",
                    "name"=>str_random(10)
                ]
            ]
        ];

        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $this->patch("countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $country->delete();
    }

    /**
     * @test
     *
     * Update country api
     *
     * @return void
     */
    public function it_should_return_error_if_id_is_invalid_for_update_country()
    {
        $params = [
            "iso"=>"",
            "translations"=>[
                [
                    "lang"=>"test",
                    "name"=>str_random(10)
                ]
            ]
        ];

        $this->patch("countries/".rand(5000000, 9000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Delete country api
     *
     * @return void
     */
    public function it_should_return_delete_country()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $this->delete("countries/".$countryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Return error for delete country api
     *
     * @return void
     */
    public function it_should_return_error_for_delete_country()
    {
        $this->delete("countries/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
}
