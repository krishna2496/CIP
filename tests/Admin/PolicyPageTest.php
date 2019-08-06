<?php

class PolicyPageTest extends TestCase
{
    /**
     * @test
     *
     * Create policy page
     *
     * @return void
     */
    public function it_should_create_policy_page()
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

        $this->post("policy/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
        ]);
        App\Models\PolicyPage::where('slug', $slug)->delete();
    }

    /**
     * @test
     *
     * Create policy page validate data
     *
     * @return void
     */
    public function it_should_show_error_for_create_policy_page_invalid_data()
    {
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

        $this->post("policy/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Get all policy pages
     *
     * @return void
     */
    public function it_should_return_all_policy_pages()
    {
        $this->get(route('policy'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Return no data found for get all policy pages
     *
     * @return void
     */
    public function it_should_return_no_data_found_for_get_all_policy_pages()
    {
        $this->get(route('policy'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }


    /**
     * @test
     *
     * Update policy page api
     *
     * @return void
     */
    public function it_should_update_policy_page()
    {
        $slug = str_random(20);
        $params = [
            'page_details' =>
                [
                'slug' => $slug
                ],
            ];

        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();
        $page_id = $policyPage->page_id;

        $this->patch("policy/".$page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
        ]);
        App\Models\PolicyPage::where('slug', $slug)->delete();
    }
    
    /**
     * @test
     *
     * Delete policy page api with already deleted or not available policy page id
     * @return void
     */
    public function it_should_return_policy_page_not_found_on_delete()
    {
        $this->delete(
            "policy/".rand(1000000, 50000000),
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
     * Update policy page api with already deleted or not available policy page id
     * @return void
     */
    public function it_should_return_policy_page_not_found_on_update()
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
            "policy/".rand(1000000, 50000000),
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
     * Return error for invalid data for policy page update
     * @return void
     */
    public function it_should_return_error_on_invalid_data_on_policy_page_update()
    {
        $slug = '';
        $params = [
            'page_details' =>
                [
                    'slug' => $slug
                ],
            ];

        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();
        $page_id = $policyPage->page_id;

        $this->patch("policy/".$page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $policyPage->delete();
    }

    /**
     * @test
     *
     * Delete policy page
     *
     * @return void
     */
    public function it_should_delete_policy_page()
    {
        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $this->delete(
            "policy/".$policyPage->page_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);
    }
    
    /**
     * @test
     *
     * Get policy page by id
     *
     * @return void
     */
    public function it_should_return_policy_page_by_id()
    {
        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $this->get('policy/'.$policyPage->page_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message",
            "data" => [
                "page_id",
                "slug",
                "status"
            ]
        ]);
        $policyPage->delete();
    }

    /**
     * @test
     *
     * Return error if privacy policy page id is invalid
     *
     * @return void
     */
    public function it_should_return_error_not_found_for_invalid_policy_page_id()
    {
        $this->get('policy/'.rand(1000000, 5000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
