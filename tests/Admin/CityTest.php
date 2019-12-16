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
        /* Add country end */
        
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
    }

    /**
     * @test
     */
    public function city_test_it_should_return_required_filed_validation_error_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
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
    public function city_test_it_should_return_validation_error_for_language_code_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
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
    public function city_test_it_should_return_city_name_exist_error_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
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
     */
    public function city_test_it_should_create_city()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
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
    public function city_test_it_should_return_error_country_invalid_on_city_create()
    {
        // Get all languages
        DB::setDefaultConnection('mysql');
        
        /* Add country start */
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
                            "lang" => $tenantLanguage->code,
                            "name" => str_random(5)
                        ]
                    ]
                ]         
            ]
        ];

        $response = $this->post("cities", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        /* Add city details end */

        // Delete tenant language
        DB::setDefaultConnection('mysql');
        
        DB::table('tenant_language')
        ->where('tenant_language_id', $tenantLanguage->tenant_language_id)
        ->delete();
        /* Delete country language end */
    }
}
