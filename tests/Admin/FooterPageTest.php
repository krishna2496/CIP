<?php

class FooterPageTest extends TestCase
{
    /**
     * @test
     *
     * Create footer page api
     *
     * @return void
     */
    public function it_should_create_footer_page()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'slug' => $slug,
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("cms/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
        ]);    
        App\Models\FooterPage::where('slug', $slug)->delete();    
    }

    /**
     * @test
     *
     * Get all footer pages
     *
     * @return void
     */
    public function it_should_return_all_footer_pages()
    {
        $titile = str_random(20);
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'slug' => $slug,
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => $titile,
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("cms/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);    
        DB::setDefaultConnection('mysql');
        $this->get('cms?search='.$titile, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\FooterPage::where('slug', $slug)->delete(); 
    }

    /**
     * @test
     *
     * Create footer page validate data
     *
     * @return void
     */
    public function it_should_show_error_for_create_footer_page_invalid_data()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'slug' => '',
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("cms/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Update footer page api
     *
     * @return void
     */
    public function it_should_update_footer_page()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'status' => 1,
                'slug' => $slug,
                'translations' =>[  
                        [
                            "lang" => "en",
                            "title" => str_random(20),
                            "sections" => [
                                [
                                    "title" => str_random(20),
                                    "description"=> str_random(20)
                                ]                                
                            ],
                        ]  
                    ]             
                ],
            ];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $pageId = $footerPage->page_id;

        $this->patch("cms/".$pageId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
        ]);
        App\Models\FooterPage::where('slug', $slug)->delete(); 
    }
    
    /**
     * @test
     *
     * Delete footer page
     *
     * @return void
     */
    public function it_should_delete_footer_page()
    {
        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();

        $this->delete(
            "cms/".$footerPage->page_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete footer page api with already deleted or not available footer page id
     * @return void
     */
    public function it_should_return_footer_page_not_found_on_delete()
    {
        $this->delete(
            "cms/".rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
    }

    /**
     * @test
     *
     * Update footer page api with already deleted or not available footer page id
     * @return void
     */
    public function it_should_return_footer_page_not_found_on_update()
    {
        $params = [
            'page_details' =>
                [
                'slug' => str_random(20),
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' => [
                            [
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ],[
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ]
                        ] 
                    ]
                ],
            ],
        ];
        
        $this->patch(
            "cms/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
    }

    /**
     * @test
     *
     * Return invalid argument error on get footer page listing
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_on_footer_page_listing()
    {
        $this->get('/cms?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(400)
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
     * Show footer page
     *
     * @return void
     */
    public function it_should_show_footer_page()
    {
        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();

        $this->get('cms/'.$footerPage->page_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        
        $footerPage->delete();
    }
    
    /**
     * @test
     *
     * Show error for invalid footer page id
     *
     * @return void
     */
    public function it_should_show_footer_page_not_found_error()
    {
        $this->get('cms/'.rand(1000000, 50000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
    }

    /**
     * @test
     *
     * Validate slug on create footer page api
     *
     * @return void
     */
    public function it_should_validate_slug_on_create_footer_page()
    {
        $params = [
            'page_details' =>
                [
                'slug' => "",
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => array(str_random(255)),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("cms/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);  
    }

    /**
     * @test
     *
     * Validate slug on update footer page api
     *
     * @return void
     */
    public function it_should_validate_slug_on_update_footer_page()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                    'slug' => ""
                ]
            ];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $pageId = $footerPage->page_id;

        $this->patch("cms/".$footerPage->page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $footerPage->delete();
    }

    /**
     * @test
     *
     * Validate request on update footer page api
     *
     * @return void
     */
    public function it_should_validate_request_on_update_footer_page()
    {
        $slug = str_random(20);
        $params = [];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $pageId = $footerPage->page_id;

        $this->patch("cms/".$footerPage->page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $footerPage->delete();
    }
}
