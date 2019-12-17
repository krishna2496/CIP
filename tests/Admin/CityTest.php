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
        
        DB::setDefaultConnection('mysql');

        /* Add city details start */     
        $cityName = str_random(5);   
        $params = [
            "country_id" => $countryId,
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

        $this->get('/entities/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);        

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

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * No data found for city 
     *
     * @return void
     */
    public function city_test_it_should_return_no_city_found_for_country_id()
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

        $this->get('/entities/cities/'.rand(900000000000,90000000000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

    }

    /**
     * @test
     * 
     * Return error for invalid token
     *
     * @return void
     */
    public function city_test_it_should_return_error_for_invalid_authorization_token_for_get_city()
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

        $this->get('/entities/cities/'.$countryId, ['Authorization' => ''])
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
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

    }

    /**
     * @test
     *
     * No data found for city 
     *
     * @return void
     */
    public function city_test_it_should_return_no_city_found()
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
        
        DB::setDefaultConnection('mysql');

        $res = $this->get('/entities/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("entities/countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

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
        
        DB::setDefaultConnection('mysql');

        /* Add city details start */        
        $params = [
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
        
        DB::setDefaultConnection('mysql');

        /* Add city details start */        
        $params = [
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

        DB::setDefaultConnection('mysql');

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
