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
        $this->get(route('cms'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
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
                'slug' => $slug                
                ],
            ];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $page_id = $footerPage->page_id;

        $this->patch("cms/".$page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                        'sections' =>  [
                            'title' => str_random(20),
                            'description' => array(str_random(255)),
                        ],
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
}
