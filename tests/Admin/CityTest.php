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
    public function city_test_it_should_return_all_city_list()
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
        /* Add country end */

        \DB::setDefaultConnection('mysql');
        /* Add state details start */
        $stateName = str_random(5);
        $params = [
            "country_id" => $countryId,
            "states" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');

        /* Add city details start */
        $cityName = str_random(5);
        $params = [
            "country_id" => $countryId,
            "state_id" => $stateId,
            "cities" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => $cityName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $cityId = json_decode($response->response->getContent())->data->city_ids[0]->city_id;
        /* Add city details end */

        DB::setDefaultConnection('mysql');

        // Get all cities
        $this->get('/entities/cities?search='.$cityName, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        // Get all cities
        $this->get('/entities/cities', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        /* Delete city details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/cities/$cityId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete city details end */

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Get city
     *
     * @return void
     */
    public function city_test_it_should_return_a_city()
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

        $mockCity = $this->getMockCityPostPayload($countryId, str_random(5));

        $reqCity = $this->post(
            'entities/cities',
            $mockCity,
            $authorization
        )->seeStatusCode(201);

        // 4: Get the created city Id

        $cityId = json_decode($reqCity->response->getContent())->data->city_ids[0]->city_id;

        // 5: Get the specific city id using the endpoint

        DB::setDefaultConnection('mysql');

        $reqSpecificCity = $this->get(
            "entities/cities/$cityId",
            $authorization
        )->seeStatusCode(200);

        // 6: assert if all fields is equal to the expected data

        $actualResult = json_decode($reqSpecificCity->response->getContent());

        $this->assertEquals($mockCity['country_id'], $actualResult->data->country_id);
        $this->assertEquals(count($mockCity['cities'][0]['translations']), count($actualResult->data->languages));

        foreach ($mockCity['cities'][0]['translations'] as $key => $trans) {
            $this->assertEquals($trans['lang'], $actualResult->data->languages[$key]->language_code);
            $this->assertEquals($trans['name'], $actualResult->data->languages[$key]->name);
        }

        // 7: Delete created city and country

        DB::setDefaultConnection('mysql');

        $this->delete(
            "entities/cities/$cityId",
            [],
            $authorization
        )->seeStatusCode(204);

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
     * Get invalid city
     *
     * @return void
     */
    public function city_test_it_should_return_invalid_city()
    {

        $authorization = [
            'Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))
        ];

        DB::setDefaultConnection('mysql');

        $reqSpecificCountry = $this->get(
            'entities/cities/0',
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
        ]);;

        $actualResult = json_decode($reqSpecificCountry->response->getContent());

        $this->assertEquals($actualResult->errors[0]->type, 'Not Found');
        $this->assertEquals($actualResult->errors[0]->code, config('constants.error_codes.ERROR_CITY_NOT_FOUND'));

    }

    /**
     * @test
     *
     * Must return error 401 Unauthorized request
     *
     * @return void
     */

    public function city_test_it_should_return_unauthorized_request_for_get_city()
    {

        DB::setDefaultConnection('mysql');

        $reqCountryCities = $this->get(
            'entities/cities/0',
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
    private function getMockCityPostPayload($countryId, $name = 'sample name')
    {
        return [
            'country_id' => $countryId,
            'cities' => [
                [
                    'translations' => [
                        [
                            'lang' => 'en',
                            'name' => $name
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function city_test_it_should_return_required_filed_validation_error_on_city_create()
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
        /* Add country end */

        \DB::setDefaultConnection('mysql');
        /* Add state details start */
        $stateName = str_random(5);
        $params = [
            "country_id" => $countryId,
            "states" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');

        /* Add city details start */
        $params = [
            "state_id" => $stateId,
            "country_id" => $countryId,
            "cities" => [
                [
                    "translations" => [
                        [
                            "lang" => "",
                            "name" => ""
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/entities/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_for_language_code_on_city_create()
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
        /* Add country end */
        \DB::setDefaultConnection('mysql');
        /* Add state details start */
        $stateName = str_random(5);
        $params = [
            "country_id" => $countryId,
            "states" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');

        /* Add city details start */
        $params = [
            "state_id" => $stateId,
            "country_id" => $countryId,
            "cities" => [
                [
                    "translations" => [
                        [
                            "lang" => str_random(5),
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/entities/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     */
    public function city_test_it_should_create_city()
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
        /* Add country end */

        \DB::setDefaultConnection('mysql');
        /* Add state details start */
        $stateName = str_random(5);
        $params = [
            "country_id" => $countryId,
            "states" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');

        /* Add city details start */
        $params = [
            "country_id" => $countryId,
            "cities" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/entities/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     */
    public function city_test_it_should_return_error_country_invalid_on_city_create()
    {
        $countryId = rand(800000000,8000000000);
        /* Add country end */

        DB::setDefaultConnection('mysql');

        /* Add city details start */
        $params = [
            "country_id" => $countryId,
            "cities" => [
                [
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        /* Add city details end */
    }

    /**
     * @test
     *
     * Update city api
     *
     * @return void
     */
    public function city_test_it_should_update_city()
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

        $this->patch("entities/cities/".$city->city_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_if_data_is_invalid_for_update_city()
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

        $this->patch("entities/cities/".$city->city_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_if_id_is_invalid_for_update_city()
    {
        $this->patch("entities/cities/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_delete_city()
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
        $this->delete("entities/cities/".$city->city_id, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    public function city_test_it_should_return_error_for_delete_city()
    {
        $this->delete("entities/cities/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Delete city api, will return error. If city belongs to mission or user
     *
     * @return void
     */
    public function city_test_it_return_error_not_able_to_delete_city_it_belongs_to_user()
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

        $this->delete("entities/cities/".$city->city_id, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        App\User::where('user_id', $user->user_id)->delete();
        App\Models\City::where('city_id', $city->city_id)->delete();
        App\Models\Country::where('country_id', $countryId)->delete();
    }

    /**
     * @test
     *
     * Delete country api, will return error. If country belongs to mission
     *
     * @return void
     */
    public function city_test_it_return_error_not_able_to_delete_city_it_belongs_to_mission()
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
}
