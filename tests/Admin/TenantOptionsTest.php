<?php

class TenantOptionsTest extends TestCase
{
    /**
     * @test
     *
     * Create slider
     *
     * @return void
     */
    public function it_should_create_slider()
    {
        $params = [
            'url' => "http://new.anasource.com/team11/s3/sliderimg4.jpg",
            'sort_order' => "1",
            'slider_detail' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'slider_title' => str_random(20),
                        'slider_description' => str_random(200)
                    ]
                ],
            ],
        ];

        $connection = 'tenant';
        $tenant = factory(\App\Models\TenantOption::class)->make();
        $tenant->setConnection($connection);
        $count = $tenant->where('option_name', config('constants.TENANT_OPTION_SLIDER'))->count();

        if ($count >= config('constants.SLIDER_LIMIT')) {
            $this->post("slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
            ->seeStatusCode(403)
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
        } else {
            $this->post("slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
            ->seeStatusCode(201)
            ->seeJsonStructure([
                'status',
                'message',
                ]);
        }
        App\Models\TenantOption::where("option_name", "slider")->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Reset style to default
     *
     * @return void
     */
    public function it_should_reset_style_to_default()
    {
        $this->get('style/reset-style', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Validate URL
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_url()
    {
        $params = [
            'url' => "test",
            'sort_order' => "1",
            'slider_detail' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'slider_title' => str_random(20),
                        'slider_description' => str_random(200)
                    ]
                ],
            ],
        ];

        $this->post("slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Download style and images from s3
     *
     * @return void
     */
    public function it_should_download_style_from_s3()
    {
        $this->get('style/download-style', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
            ]);
    }

    /**
    * @test
    *
    * Update style
    *
    * @return void
    */
    public function it_should_return_error_for_missing_file_while_update_style()
    {
        $this->post('style/update-style', [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
    * @test
    *
    * Get custom styling css
    *
    * @return void
    */
    public function it_should_return_custom_css()
    {
        $this->get('app/custom-css', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Create tenant option
     *
     * @return void
     */
    public function it_should_create_tenant_option()
    {
        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\TenantOption::where("option_name", $optionName)->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if data is invalid
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_data_for_tenant_option()
    {
        $optionName = '';
        $params = [
            'option_name' => $optionName,
            'option_value' =>
                [
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'message' => str_random(20)
                    ]
                ],
            ],
        ];

        $this->post("tenant-option/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
}
