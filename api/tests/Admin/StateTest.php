<?php
use App\Helpers\Helpers;

class StateTest extends TestCase
{
    /**
     * @test
     *
     * Get state list
     *
     * @return void
     */
    public function state_test_it_should_return_all_state_list()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');

        $this->get('/entities/countries/'.$countryId.'/states', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        DB::setDefaultConnection('mysql');

        // Get all states
        $this->get('/entities/states?search=' . $stateName, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        // Get all states
        $this->get('/entities/states', ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * No data found for state
     *
     * @return void
     */
    public function state_test_it_should_return_no_state_found_for_country_id()
    {
        DB::setDefaultConnection('mysql');

        $this->get('/entities/states/' . rand(900000000000, 90000000000000), ['Authorization' => Helpers::getBasicAuth()])
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
        DB::setDefaultConnection('mysql');
    }

    /**
     * @test
     *
     * Return error for invalid token
     *
     * @return void
     */
    public function state_test_it_should_return_error_for_invalid_authorization_token_for_get_state()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;

        DB::setDefaultConnection('mysql');

        $this->get('/entities/states/' . $countryId, ['Authorization' => ''])
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

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * No data found for state
     *
     * @return void
     */
    public function state_test_it_should_return_no_state_found()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        /* Add country end */

        DB::setDefaultConnection('mysql');

        $this->get('/entities/states/' . $countryId, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(404);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * return validation error on state create
     *
     * @return void
     */
    public function state_test_it_should_return_required_field_validation_error_on_state_create()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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
                            "name" => ''
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * validation error for language code on state create
     *
     * @return void
     */
    public function state_test_it_should_return_validation_error_for_language_code_on_state_create()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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
                            "lang" => str_random(5),
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     */
    public function state_test_it_should_create_state()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        /* Delete state details start */
        DB::setDefaultConnection('mysql');
        // Delete state and state_language data
        $this->delete("entities/states/$stateId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
        /* Delete state details end */


        DB::setDefaultConnection('mysql');
        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     */
    public function state_test_it_should_return_error_country_invalid_on_state_create()
    {
        $countryId = rand(800000000, 8000000000);
        /* Add country end */

        DB::setDefaultConnection('mysql');

        /* Add state details start */
        $params = [
            "country_id" => $countryId,
            "states" => [
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

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        /* Add state details end */
    }

    /**
     * @test
     *
     * Update state api
     *
     * @return void
     */
    public function state_test_it_should_update_state()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $state = factory(\App\Models\State::class)->make();
        $state->setConnection($connection);
        $state->save();
        $state->country_id = $countryId;
        $state->update();

        $params = [
            "country_id" => $countryId,
            "translations" => [
                [
                    "lang" => "en",
                    "name" => str_random(10)
                ]
            ]
        ];

        $this->patch("entities/states/" . $state->state_id, $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
        ]);
        App\Models\Country::where('country_id', $countryId)->delete();
        App\Models\state::where('state_id', $state->state_id)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid for update state api
     *
     * @return void
     */
    public function state_test_it_should_return_error_if_data_is_invalid_for_update_state()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $state = factory(\App\Models\State::class)->make();
        $state->setConnection($connection);
        $state->save();
        $state->country_id = $countryId;
        $state->update();

        $params = [
                "country_id" => "",
                "translations" => [
                   [
                      "lang" => "en",
                      "name" => ""
                   ]
                ]
        ];

        $this->patch("entities/states/" . $state->state_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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
        App\Models\state::where('state_id', $state->state_id)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid for update state api
     *
     * @return void
     */
    public function state_test_it_should_return_error_if_id_is_invalid_for_update_state()
    {
        $this->patch("entities/states/" . rand(1000000, 5000000), [], ['Authorization' => Helpers::getBasicAuth()])
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
     * Delete state api
     *
     * @return void
     */
    public function state_test_it_should_delete_state()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $state = factory(\App\Models\State::class)->make();
        $state->setConnection($connection);
        $state->save();
        $state->country_id = $countryId;
        $state->update();

        DB::setDefaultConnection('mysql');
        $this->delete("entities/states/" . $state->state_id, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
        App\Models\Country::where('country_id', $countryId)->delete();
    }

    /**
     * @test
     *
     * Return error for delete state api
     *
     * @return void
     */
    public function state_test_it_should_return_error_for_delete_state()
    {
        $this->delete("entities/states/" . rand(1000000, 5000000), [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(404);
    }


    /**
     * @test
     *
     * Delete country api, will return error. If country belongs to mission
     *
     * @return void
     */
    public function state_test_it_return_error_not_able_to_delete_state_it_belongs_to_mission()
    {
        $connection = 'tenant';
        $state = factory(\App\Models\State::class)->make();
        $state->setConnection($connection);
        $state->save();
        $stateId = $state->state_id;

        $city = factory(\App\Models\City::class)->make();
        $city->setConnection($connection);
        $city->save();
        $city->state_id = $stateId;
        $city->update();
        $cityId = $city->city_id;

        DB::setDefaultConnection('mysql');

        // Add user for this country and state
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $mission->city_id = $cityId;
        $mission->update();

        $res = $this->delete("entities/states/" . $stateId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        App\Models\Mission::where('mission_id', $mission->mission_id)->delete();

        App\Models\City::where('city_id', $cityId)->delete();

        App\Models\State::where('state_id', $stateId)->delete();
    }

    /**
     * @test
     *
     * Get state list by country id
     *
     * @return void
     */
    public function state_test_it_should_return_all_state_by_country_id()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');
        // Get all states
        $this->get('/entities/countries/' . $countryId . '/states', ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/" . $stateId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
        /* Delete state details end */

        DB::setDefaultConnection('mysql');
        // Get all states
        $this->get('/entities/countries/' . $countryId . '/states', ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/" . $countryId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Get state list by state id
     *
     * @return void
     */
    public function state_test_it_should_return_all_state_by_state_id()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);
        $stateId = json_decode($response->response->getContent())->data->state_ids[0]->state_id;

        DB::setDefaultConnection('mysql');
        // Get all states
        $this->get('/entities/states/' . $stateId, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(200);

        /* Delete state details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/states/" . $stateId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
        /* Delete state details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Return state not found error on country id
     *
     * @return void
     */
    public function state_test_it_should_return_state_not_found_on_country_id()
    {
        DB::setDefaultConnection('mysql');
        // Get all states by country id
        $this->get('/entities/countries/' . rand(10000, 99999) . '/states', ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * invalid language codeon state create
     *
     * @return void
     */
    public function state_test_it_should_return_invalid_language_code_for_tenant_on_state_create()
    {
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations" => [
                        [
                            "lang" => "en",
                            "name" => str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/countries", $params, ['Authorization' => Helpers::getBasicAuth()])
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
                            "lang" => str_random(2),
                            "name" => $stateName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("entities/states", $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * invalid language code on state update
     *
     * @return void
     */
    public function state_test_it_should_return_invalid_language_code_for_tenant_on_state_update()
    {
        $connection = 'tenant';
        $country = factory(\App\Models\Country::class)->make();
        $country->setConnection($connection);
        $country->save();
        $countryId = $country->country_id;

        $state = factory(\App\Models\State::class)->make();
        $state->setConnection($connection);
        $state->save();
        $state->country_id = $countryId;
        $state->update();

        $params = [
                "country_id" => $countryId,
                "translations" => [
                   [
                      "lang" => str_random(2),
                      "name" => str_random(5)
                   ]
                ]
        ];

        $this->patch("entities/states/" . $state->state_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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
        App\Models\state::where('state_id', $state->state_id)->delete();
    }
}
