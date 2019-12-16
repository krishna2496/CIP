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
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()->first();
        
        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')
            ->whereNull('deleted_at')
            ->inRandomOrder()
            ->first();

            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
            ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                            "lang" => $tenantLanguage->code,
                            "name" => str_random(5)
                        ]
                    ]
                ]         
            ]
        ];

        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $cityId = json_decode($response->response->getContent())->data->city_ids[0]->city_id;
        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete city details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("cities/$cityId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete city details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */
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
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.rand(900000000000,90000000000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();        
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
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        
        DB::setDefaultConnection('mysql');

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

        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();  
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
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        /* Add country end */
        
        DB::setDefaultConnection('mysql');

        DB::setDefaultConnection('mysql');

        $res = $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */

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
     */
    public function it_should_return_required_filed_validation_error_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */
    }

    /**
     * @test
     */
    public function it_should_return_validation_error_for_country_code_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        /* Add city details end */

        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */
    }

    /**
     * @test
     */
    public function it_should_return_city_name_exist_error_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->whereNull('deleted_at')->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->whereNull('tenant_language.deleted_at')
        ->whereNull('language.deleted_at')
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => str_random(2),
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $countryId = json_decode($response->response->getContent())->data->country_ids[0]->country_id;
        /* Add country end */
        
        DB::setDefaultConnection('mysql');
        $cityName = str_random(5);

        /* Add city details start */        
        $params = [
            "country_id" => $countryId,
            "cities" => [ 
                [ 
                    "translations" => [ 
                        [ 
                            "lang" => $tenantLanguage->code,
                            "name" => $cityName
                        ]
                    ]
                ]         
            ]
        ];
        
        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $cityId = json_decode($response->response->getContent())->data->city_ids[0]->city_id;
        /* Add city details end */
        
        /* Add another city details with same details start */        
        DB::setDefaultConnection('mysql');
        
        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        /* Add another city details with same details start */

        DB::setDefaultConnection('mysql');

        $this->get('/cities/'.$countryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        /* Delete city details start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("cities/$cityId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        /* Delete city details end */

        /* Delete country language start */
        DB::setDefaultConnection('mysql');

        // Delete country and country_language data
        $this->delete("countries/$countryId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */        
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
        
        DB::setDefaultConnection('mysql');
        // Get all cities
        $this->get('/cities', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
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
