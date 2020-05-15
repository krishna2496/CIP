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
    public function city_test_it_should_return_all_country_list()
    {
        // Get random langauge for country name
        $countryName = str_random(5);
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> $countryName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

        $this->get('/entities/countries?search='.$countryName, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');

        $this->get('/entities/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "*" => []
            ],
            "message"
        ]);
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Get country
     *
     * @return void
     */
    public function country_test_it_should_return_a_country()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        // 1: Create new country

        $mockCountry = $this->getMockCountryPostPayload();

        $reqCountry = $this->post(
            'entities/countries', 
            $mockCountry, 
            $authorization
        )->seeStatusCode(201);

        // 2: Get the created country id

        $countryId = json_decode($reqCountry->response->getContent())->data->country_ids[0]->country_id;

        // 3: Get the specific country id using the endpoint

        DB::setDefaultConnection('mysql');

        $reqSpecificCountry = $this->get(
            "entities/countries/$countryId",
            $authorization
        )->seeStatusCode(200);

        // 4: assert if all fields is equal to the expected data

        $actualResult = json_decode($reqSpecificCountry->response->getContent());

        $this->assertEquals(strtoupper($mockCountry['countries'][0]['iso']), $actualResult->data->ISO);
        $this->assertEquals(count($mockCountry['countries'][0]['translations']), count($actualResult->data->languages));

        foreach ($mockCountry['countries'][0]['translations'] as $key => $trans) {
            $this->assertEquals($trans['lang'], $actualResult->data->languages[$key]->language_code);
            $this->assertEquals($trans['name'], $actualResult->data->languages[$key]->name);
        }

        // 5: Delete created country

        DB::setDefaultConnection('mysql');

        $this->delete(
            "entities/countries/$countryId",
            [],
            $authorization
        )->seeStatusCode(204);

    }

    /**
     * @test
     *
     * Get invalid country
     *
     * @return void
     */
    public function country_test_it_should_return_invalid_country()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        DB::setDefaultConnection('mysql');

        $reqSpecificCountry = $this->get(
            'entities/countries/0',
            $authorization
        )->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'message'
                ]
            ]
        ]);

        $actualResult = json_decode($reqSpecificCountry->response->getContent());

        $this->assertEquals($actualResult->errors[0]->type, 'Not Found');
        $this->assertEquals($actualResult->errors[0]->code, config('constants.error_codes.ERROR_COUNTRY_NOT_FOUND'));

    }

    /**
     * @test
     *
     * Must return error 401 Unauthorized request
     *
     * @return void
     */
    public function country_test_it_should_return_unauthorized_request_for_get_country()
    {

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            'entities/countries'
        )->seeStatusCode(401)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'message'
                ]
            ]
        ]);

    }

    /**
     * @test
     *
     * Get country cities
     *
     * @return void
     */
    public function country_test_it_should_return_list_of_country_cities()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        // 1: Create new country

        $mockCountry = $this->getMockCountryPostPayload();

        $reqCountry = $this->post(
            'entities/countries', 
            $mockCountry, 
            $authorization
        )->seeStatusCode(201);

        // 2: Get the created country id

        $countryId = json_decode($reqCountry->response->getContent())->data->country_ids[0]->country_id;

        // 3: Create a city based on the created country id

        DB::setDefaultConnection('mysql');

        $mockCities = $this->getMockCityPostPayload($countryId, 2);

        $reqCity = $this->post(
            'entities/cities', 
            $mockCities, 
            $authorization
        )->seeStatusCode(201);

        $reqCityResponse = json_decode($reqCity->response->getContent());

        // 4: Get all cities of the created country

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            "entities/countries/$countryId/cities",
            $authorization
        )->seeStatusCode(200);

        $reqCountryCitiesRes = json_decode($reqCountryCities->response->getContent());


        // 5: assert if all fields is equal to the expected data

        foreach ($mockCities['cities'] as $key => $city) {
            foreach ($city['translations'] as $transKey => $trans) {
                $this->assertEquals($trans['lang'], $reqCountryCitiesRes->data[$key]->languages[$transKey]->language_code);
                $this->assertEquals($trans['name'], $reqCountryCitiesRes->data[$key]->languages[$transKey]->name);
            }
        }

        // 6: Delete created city and country

        foreach ($reqCityResponse->data->city_ids as $key => $item) {

            $cityId = $item->city_id;
            
            DB::setDefaultConnection('mysql');

            $this->delete(
                "entities/cities/$cityId",
                [],
                $authorization
            )->seeStatusCode(204);

        }

        DB::setDefaultConnection('mysql');

        $this->delete(
            "entities/countries/$countryId",
            [],
            $authorization
        )->seeStatusCode(204);

    }

    /**
     * @test
     *
     * Must return country id error on get country cities
     *
     * @return void
     */
    public function country_test_it_should_return_error_for_invalid_country_id_on_country_cities()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            'entities/countries/0/cities',
            $authorization
        )->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'message'
                ]
            ]
        ]);

    }

    /**
     * @test
     *
     * Get no cities
     *
     * @return void
     */
    public function country_test_it_should_return_no_city_found()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        // 1: Create new country

        $mockCountry = $this->getMockCountryPostPayload();

        $reqCountry = $this->post(
            'entities/countries', 
            $mockCountry, 
            $authorization
        )->seeStatusCode(201);

        // 2: Get the created country id

        $countryId = json_decode($reqCountry->response->getContent())->data->country_ids[0]->country_id;

        // 4: Get all cities of the created country

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            "entities/countries/$countryId/cities",
            $authorization
        )->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data",
            "message"
        ]);

        $reqCountryCitiesRes = json_decode($reqCountryCities->response->getContent());

        $this->assertEquals($reqCountryCitiesRes->data, []);
        $this->assertEquals($reqCountryCitiesRes->message, 'City not found');

        // Delete created country

        DB::setDefaultConnection('mysql');

        $this->delete(
            "entities/countries/$countryId",
            [],
            $authorization
        )->seeStatusCode(204);

    }

    /**
     * @test
     *
     * Must return error 401 Unauthorized request
     *
     * @return void
     */
    public function country_test_it_should_return_unauthorized_request_for_country_cities()
    {

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            'entities/countries/0/cities'
        )->seeStatusCode(401)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'message'
                ]
            ]
        ]);

    }

    /**
     * Get country post parameters
     *
     * @return array
     */
    private function getMockCountryPostPayload()
    {
        return [
            'countries' => [
                [
                    'iso' => str_random(2),
                    'translations'=> [
                        [
                            'lang'=> 'en',
                            'name'=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get city post parameters
     *
     * @return array
     */
    private function getMockCityPostPayload($countryId, $count = 1)
    {
        $data = [
            'country_id' => $countryId,
            'cities' => []
        ];

        for ($i = 1; $i <= $count; $i++) { 
            $data['cities'][] = [ 
                'translations' => [ 
                    [ 
                        'lang' => 'en',
                        'name' => uniqid()
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * @test
     *
     * No data found for Country
     *
     * @return void
     */
    public function city_test_it_should_return_no_country_found()
    {
        $this->get('/entities/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     */
    public function city_test_it_should_create_and_delete_country()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }    

    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_for_iso_code_on_add_country()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => '',
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

    }

    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_for_language_code_on_add_country()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> str_random(5),
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

    }
    
    /**
     * @test
     *
     * Update country api
     *
     * @return void
     */
    public function city_test_it_should_update_country()
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

        $this->patch("entities/countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_if_iso_is_invalid_for_update_country()
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

        $this->patch("entities/countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_if_data_is_invalid_for_update_country()
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

        $this->patch("entities/countries/".$countryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_if_id_is_invalid_for_update_country()
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

        $this->patch("entities/countries/".rand(5000000, 9000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_delete_country()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $this->delete("entities/countries/".$countryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Return error for delete country api
     *
     * @return void
     */
    public function city_test_it_should_return_error_for_delete_country()
    {
        $this->delete("entities/countries/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
    
    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_on_iso_exist_on_add_country()
    {
        $countryISO = str_random(2);
        
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

        // Add another country with same ISO code        
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];
        
        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    } 

    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_on_country_name_exist_on_add_country()
    {
        $countryName = str_random(5);
        $countryISO = str_random(2);

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> $countryName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

        // Add another country with same ISO code        
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> $countryName
                        ]
                    ]
                ]
            ]
        ];
        
        $response = $this->post("entities/countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');
        
        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete country api, will return error. If country belongs to mission
     *
     * @return void
     */
    public function city_test_it_return_error_not_able_to_delete_country_it_belongs_to_mission()
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
        
        DB::setDefaultConnection('mysql');

        // Add user for this country and city
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $mission->city_id = $city->city_id;
        $mission->country_id = $countryId;
        $mission->update();

        $res = $this->delete("entities/countries/".$countryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);        

        App\Models\Mission::where('mission_id', $mission->mission_id)->delete();
        App\Models\City::where('city_id', $city->city_id)->delete();
        App\Models\Country::where('country_id', $countryId)->delete();
    }

    /**
     * @test
     *
     * Delete country api, will return error. If country belongs to user
     *
     * @return void
     */
    public function city_test_it_return_error_not_able_to_delete_country_it_belongs_to_user()
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
        
        DB::setDefaultConnection('mysql');

        // Add user for this country and city
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $user->city_id = $city->city_id;
        $user->country_id = $countryId;
        $user->update();

        $this->delete("entities/countries/".$countryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        App\User::where('user_id', $user->user_id)->delete();
        App\Models\City::where('city_id', $city->city_id)->delete();
        App\Models\Country::where('country_id', $countryId)->delete();
    }
}
