<?php
use App\Helpers\Helpers;
use App\Models\News;
use App\Models\NewsCategory;

class NewsCategoryTest extends TestCase
{
    /**
     * @test
     *
     * Get listing of all news categories
     *
     * @return void
     */
    public function news_category_test_it_should_return_news_categories()
    {
        // List of news categories
        $this->get('news/category', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        
        // When there is no category available
        News::whereNull('deleted_at')->delete();

        \DB::setDefaultConnection('mysql');        
        $this->get('news/category?search=&order=desc', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message'
        ]);

        News::whereNotNull('deleted_at')->update(['deleted_at' => null]);
    }

    /**
     * @test
     *
     * It should create news category
     *
     * @return void
     */
    public function news_category_test_it_should_create_news_category()
    {
        // Create news category
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();

        // Validation error for category name, it must required
        $params = [
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        $newsCategoryDetails = NewsCategory::first();

        // Validation error for category name, it must be unique
        $params = [
            "category_name" => $newsCategoryDetails->category_name,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        // Validation error for translations, it must be required
        $params = [
            "category_name" => str_random('5'),
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        // Validation error for translations' translation code, it must be language code with 2 character
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => str_random('5'),
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        // Validation error for translations' translation title, it should required
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     * 
     * It should news category by category_id
     * 
     * @return void
     */
    public function news_category_test_it_should_get_news_category_by_category_id()
    {
        // Create news category
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Get details of category, which is create above
        \DB::setDefaultConnection('mysql');

        $this->get('news/category/'.$newsCategoryId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data' => [
                'news_category_id',
                'category_name',
                'translations' => [
                    "*" => [
                        'lang',
                        'title'
                    ]
                ]
            ],
            'message'
        ]);

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();

        // It should give an error while trying to get details of unavailable news category id
        \DB::setDefaultConnection('mysql');

        $this->get('news/category/'.rand(50000000000,500000000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should update news category
     *
     * @return void
     */
    public function news_category_test_it_should_update_news_category()
    {
        // Update news category        
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Update created category content
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$newsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);        

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();
    }
    /**
     * @test
     *
     * It should update news category
     *
     * @return void
     */
    public function news_category_test_it_should_return_error_category_id_not_available_on_update_news_category()
    {
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];
        $randNewsCategoryId = rand(50000000000,900000000000);
        $response = $this->patch('news/category/'.$randNewsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should update news category
     *
     * @return void
     */
    public function news_category_test_it_should_return_error_category_field_is_require_on_update_news_category()
    {
        // Update news category        
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Update created category content
        $params = [
            "category_name" => "",
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$newsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();
    }

    /**
     * @test
     *
     * It should return error language code invalid on update news category
     *
     * @return void
     */
    public function news_category_test_it_should_return_error_language_code_invalid_on_update_news_category()
    {
        // Update news category        
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Language code must be 2 characters validation
        $params = [
            "category_name" => str_random('5'),
            "translations" => [
                [
                    "lang" => str_random('5'),
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$newsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();
    }

    /**
     * @test
     *
     * It should return error category name exist on update news cateogry
     *
     * @return void
     */
    public function news_category_test_it_should_return_error_category_name_exist_on_update_news_cateogry()
    {
        // Category is already taken
        $categoryName = str_random('5');        
        // Create new category
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');
        
        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $NewNewsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Use above created category name for update
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => str_random('5'),
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$NewNewsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        NewsCategory::where('news_category_id', $NewNewsCategoryId)->delete();
    }

    /**
     * @test
     *
     * It should allow deleted category name on update news category
     *
     * @return void
     */
    public function news_category_test_it_should_allow_deleted_category_name_on_update_news_category()
    {
        // Category is already taken
        $categoryName = str_random('5');        
        // Create new category
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');
        
        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;
        
        // Category is already taken
        $categoryName = str_random('5');        
        // Create new category
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');
        
        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $NewNewsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        // Use above created category name for update
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => str_random('5'),
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$NewNewsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        NewsCategory::where('news_category_id', $NewNewsCategoryId)->delete();

        // Deleted category name should allow. 
        // Use above deleted category name for update
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');

        $response = $this->patch('news/category/'.$newsCategoryId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        NewsCategory::where('news_category_id', $newsCategoryId)->delete();
    }

    /**
     * @test
     */
    public function news_category_it_should_delete_news_category()
    {
        // Category is already taken
        $categoryName = str_random('5');        
        // Create new category
        $params = [
            "category_name" => $categoryName,
            "translations" => [
                [
                    "lang" => 'en',
                    "title" => str_random('5')
                ],
                [
                    "lang" => 'fr',
                    "title" => str_random('5')
                ]
            ]
        ];

        \DB::setDefaultConnection('mysql');        
        $response = $this->post('news/category', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $newsCategoryId = json_decode($response->response->getContent())->data->news_category_id;

        \DB::setDefaultConnection('mysql');
        $this->delete('news/category/'.$newsCategoryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * It should return an error news category not found
     * 
     * @return void
     */
    public function news_category_it_should_return_error_news_category_id_not_found_on_deleted_news_category()
    {
        $newsCategoryId = rand(5000000000, 50000000000);
        $this->delete('news/category/'.$newsCategoryId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }
}
