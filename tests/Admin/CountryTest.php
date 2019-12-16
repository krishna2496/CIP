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
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
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

        $this->get('/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * No data found for Country
     *
     * @return void
     */
    public function city_test_it_should_return_no_country_found()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->inRandomOrder()
            ->first();
        }

        $this->get('/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

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
    public function city_test_it_should_return_error_for_invalid_authorization_token_for_get_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->inRandomOrder()
            ->first();
        }

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

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
    }

    /**
     * @test
     */
    public function city_test_it_should_create_and_delete_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
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
     */
    public function city_test_it_should_return_validation_error_for_iso_code_on_add_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->inRandomOrder()
            ->first();
        }

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => '',
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
        ->seeStatusCode(422);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
    }

    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_for_language_code_on_add_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
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
                            "lang"=> str_random(5),
                            "name"=> str_random(5)
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
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
    public function city_test_it_should_return_delete_country()
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
    public function city_test_it_should_return_error_for_delete_country()
    {
        $this->delete("countries/".rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
    
    /**
     * @test
     */
    public function city_test_it_should_return_validation_error_on_iso_exist_on_add_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->inRandomOrder()
            ->first();
        }
        
        $countryISO = str_random(2);
        
        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
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

        // Add another country with same ISO code        
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
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
        ->seeStatusCode(422);

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
     */
    public function city_test_it_should_return_validation_error_on_country_name_exist_on_add_country()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');

        $tenantLanguage = DB::table('tenant_language')
        ->join('language', 'tenant_language.language_id', 'language.language_id')
        ->where('tenant_id', env('DEFAULT_TENANT_ID'))
        ->inRandomOrder()
        ->first();

        if (is_null($tenantLanguage)) {
            $randomLangage = DB::table('language')->inRandomOrder()->first();
            
            $tenantLanguageId = DB::table('tenant_language')->insertGetId([
                'tenant_id' => env('DEFAULT_TENANT_ID'),
                'language_id' => $randomLangage->language_id,
                'default' => '1'
            ]);
            
            $tenantLanguage = DB::table('tenant_language')
            ->join('language', 'tenant_language.language_id', 'language.language_id')
            ->where('tenant_id', env('DEFAULT_TENANT_ID'))
            ->inRandomOrder()
            ->first();
        }
        
        $countryName = str_random(5);
        $countryISO = str_random(2);

        // Get random langauge for country name
        $params = [
            "countries" => [
                [
                    "iso" => $countryISO,
                    "translations"=> [
                        [
                            "lang"=> $tenantLanguage->code,
                            "name"=> $countryName
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                            "lang"=> $tenantLanguage->code,
                            "name"=> $countryName
                        ]
                    ]
                ]
            ]
        ];
        
        $response = $this->post("countries", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

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
}
