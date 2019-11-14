<?php
use App\Helpers\Helpers;
use App\Models\News;
use App\Models\NewsCategory;

class NewsTest extends TestCase
{
    /**
     * @test
     *
     * Get listing of all news categories
     * @return void
     */
    public function admin_news_it_should_create_news()
    {
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();
        
        DB::setDefaultConnection('tenant');
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsId = json_decode($response->response->getContent())->data->news_id;

        // When there is no category available
        News::whereNull('deleted_at')->where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }

    /**
     * @test
     * 
     * It should return validation error, for invalid category id
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_invalid_news_category_on_create()
    {
        DB::setDefaultConnection('tenant');
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => rand(50000000,500000000),
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);        
    }

    /**
     * @test
     * 
     * It should return validation error, for invalid user name is required
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_blank_user_name_on_create()
    {
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');        
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => "",
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);     
        $newsCategory->delete();   
    }

    /**
     * @test
     * 
     * It should return validation error, for invalid language code
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_invalid_language_code_on_create()
    {
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');        
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => str_random('3'),
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);       
        $newsCategory->delete();    
    }

    /**
     * @test
     * 
     * It should return validation error, for invalid news image
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_invalid_news_image_on_create()
    {
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');        
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/dummy.txt",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        $newsCategory->delete();        
    }

    /**
     * @test
     * 
     * It should return validation error, for invalid status
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_invalid_status_on_create()
    {
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');        
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => str_random('5'),
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        $newsCategory->delete();
    }

    /**
     * @test
     * 
     * It should return list of news
     * 
     * @return void
     */
    public function admin_news_it_should_news_listing()
    {
        $newsIdsArray = [];
        $connection = 'tenant';
                
        for ($i=0; $i<5; $i++) {

            $news = factory(\App\Models\News::class)->make();
            $news->setConnection($connection);
            $news->save();

            $newsCategory = factory(\App\Models\NewsCategory::class)->make();
            $newsCategory->setConnection($connection);
            $newsCategory->save();
            
            $newsToCategory = factory(\App\Models\NewsToCategory::class)->make();
            $newsToCategory->setConnection($connection);
            $newsToCategory->news_id = $news->news_id;
            $newsToCategory->news_category_id = $newsCategory->news_category_id;
            $newsToCategory->save();

            $newsLanguage = factory(\App\Models\NewsLanguage::class)->make();
            $newsLanguage->setConnection($connection);
            $newsLanguage->news_id = $news->news_id;
            $newsLanguage->save();
            
            array_push($newsIdsArray, $news->news_id); 
        } 

        $this->get('news?order=desc&search='.$newsLanguage->title, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        $this->get('news', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        DB::setDefaultConnection('mysql');
        $this->get('news?order=test&search='.$newsLanguage->title, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400);

        News::whereIn('news_id', $newsIdsArray)->delete();
    }

    /**
     * @test 
     * 
     * It should update news details
     * 
     * @return void
     */
    public function admin_news_it_should_update_news_details()
    {      
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        // Going to update details
        $params["user_name"] = str_random('5');
        $params["user_title"] = strtoupper(str_random('3'));
        $params["news_content"] = [
            "translations" => [
                [  
                    "lang" => "en",
                    "title" => "english_".str_random('10'),
                    "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                ],
                [
                    "lang" => "fr",
                    "title" => "french_".str_random('10'),
                    "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                ]
            ]
        ];
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }

    /**
     * @test 
     * 
     * It return an error when invalid news_id used on update news details
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_for_invalid_news_id_update_news_details()
    {     
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');
        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        $newsId = rand(500000000, 5000000000);
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);  
        $newsCategory->delete();      
    }

    /**
     * @test 
     * 
     * It should return validation error on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_validation_error_on_update_news_details()
    {     
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        // Going to update details
        $params["user_name"] = "";
        $params["user_title"] = "";
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();

    }

    /**
     * @test 
     * 
     * It should return validation error for language code on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_validation_error_for_language_code_on_update_news_details()
    {  
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        // Going to update details
        $params["news_content"]["translations"][0]['lang'] = str_random('3');        
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }

    /**
     * @test 
     * 
     * It should return validation error for invalid media url on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_validation_error_for_invalid_media_url_on_update_news_details()
    {    
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        // Going to update details
        $params["news_image"] = "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/dummy.txt";
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }

    /**
     * @test 
     * 
     * It should return validation error for invalid status on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_validation_error_for_invalid_status_on_update_news_details()
    {    
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        // Going to update details
        $params["status"] = str_random('5');
        
        DB::setDefaultConnection('mysql');
        $response = $this->patch('news/'.$newsId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }

    /**
     * @test
     * 
     * It should return validation error for invalid status on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_news_details()
    {   
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsId = json_decode($response->response->getContent())->data->news_id;

        DB::setDefaultConnection('mysql');
        $response = $this->get('news/'.$newsId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        News::where('news_id', $newsId)->delete();
        $newsCategory->delete();
    }


    /**
     * @test
     * 
     * It should return validation error for invalid status on update news details
     * 
     * @return void
     */
    public function admin_news_it_return_error_news_not_found_on_news_details()
    {        
        $newsId = rand(50000000, 500000000);

        DB::setDefaultConnection('mysql');
        $response = $this->get('news/'.$newsId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * It should delete news
     * 
     * @return void
     */
    public function admin_news_it_should_delete_news()
    {    
        $connection = 'tenant';
        $newsCategory = factory(\App\Models\NewsCategory::class)->make();
        $newsCategory->setConnection($connection);
        $newsCategory->save();

        DB::setDefaultConnection('tenant');

        $params = [
            "news_image" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "user_name" => str_random('5'),
            "user_title" => strtoupper(str_random('3')),
            "user_thumbnail" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/unitTestFiles/sliderimg4.jpg",
            "news_category_id" => $newsCategory->news_category_id,
            "status" => "PUBLISHED",
            "news_content" => [
                "translations" => [
                    [  
                        "lang" => "en",
                        "title" => "english_".str_random('10'),
                        "description" => "We can collect the following information: name and job title, contact information, including email address, demographic information such as zip code, preferences and interests, other relevant information for surveys and / or customer offers"
                    ],
                    [
                        "lang" => "fr",
                        "title" => "french_".str_random('10'),
                        "description" => "lNous pouvons collecter les informations suivantes: nom et intitulé du poste, informations de contact, y compris adresse électronique, informations démographiques telles que le code postal, préférences et intérêts, autres informations pertinentes pour les enquêtes et / ou les offres clients"
                    ]
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('news', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsId = json_decode($response->response->getContent())->data->news_id;

        DB::setDefaultConnection('mysql');
        $response = $this->delete('news/'.$newsId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
        $newsCategory->delete();
    }

    /**
     * @test
     * 
     * It should return error news not found on delete news
     * 
     * @return void
     */
    public function admin_news_it_should_return_error_news_not_found_on_delete_news()
    {
        $newsId = rand(50000000, 500000000);
        DB::setDefaultConnection('mysql');
        $response = $this->delete('news/'.$newsId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
}
