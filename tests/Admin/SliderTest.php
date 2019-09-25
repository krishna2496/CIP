<?php

class SliderTest extends TestCase
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
        DB::setDefaultConnection('tenant');
        App\Models\Slider::whereNotNull('slider_id')->delete();
        DB::setDefaultConnection('mysql');
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $this->post("slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\Slider::orderBy("slider_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if slider limit is reached for create slider
     *
     * @return void
     */
    public function it_should_return_error_for_create_slider()
    {
        DB::setDefaultConnection('tenant');
        App\Models\Slider::whereNotNull('slider_id')->delete();
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        for ($i = 0; $i < config('constants.SLIDER_LIMIT'); $i++) {
            DB::setDefaultConnection('mysql');
            $this->post("slider/", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
            ->seeStatusCode(201);
        }

        DB::setDefaultConnection('mysql');
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
        App\Models\Slider::whereNotNull('slider_id')->delete();
    }


    /**
     * @test
     *
     * Validate URL
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_url_for_create_slider()
    {
        $params = [
            'url' => "test",
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
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
     * Validate sort order
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_sort_order_for_create_slider()
    {
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => str_random(20),        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
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
     * Validate Language code
     *
     * @return void
     */
    public function it_should_return_error_for_validate_language_code_for_create_slider()
    {
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => 1,        
            'translations' =>  [
                [
                    'lang' => 'test',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
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
     * Update slider
     *
     * @return void
     */
    public function it_should_update_slider()
    {
        DB::setDefaultConnection('tenant');
        App\Models\Slider::whereNotNull('slider_id')->delete();
        DB::setDefaultConnection('mysql');
        
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->patch("slider/".$slider->slider_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        App\Models\Slider::orderBy("slider_id", "DESC")->take(1)->delete();
        $slider->delete();
    }

    
    /**
     * @test
     *
     * Validate URL
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_url_for_update_slider()
    {
        $params = [
            'url' => "test",
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->patch("slider/".$slider->slider_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $slider->delete();
    }

    /**
     * @test
     *
     * Validate sort order
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_sort_order_for_update_slider()
    {
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => str_random(2),        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->patch("slider/".$slider->slider_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $slider->delete();
    }

    /**
     * @test
     *
     * Validate Language code
     *
     * @return void
     */
    public function it_should_return_error_for_validate_language_code_for_update_slider()
    {
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => 1,        
            'translations' =>  [
                [
                    'lang' => 'test',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->patch("slider/".$slider->slider_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $slider->delete();
    }

    /**
     * @test
     *
     * Get all sliders
     *
     * @return void
     */
    public function it_should_return_all_sliders()
    {
        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->get('slider/', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
               "*" => [
                    "slider_id",
                    "url",
                    "translations",
                    "sort_order"
                ]
            ],
            "message"
        ]);
        $slider->delete();
    }

    /**
     * @test
     *
     * No slider found
     *
     * @return void
     */
    public function it_should_return_no_slider_found()
    {
        $this->get('slider/', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * It should delete slider
     *
     * @return void
     */
    public function it_should_delete_slider()
    {
        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->delete('slider/'.$slider->slider_id, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * It should return error for invalid slider id for delete slider
     *
     * @return void
     */
    public function it_should_return_error_if_slider_id_is_invalid()
    {   
        $this->delete('slider/'.rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
     * Validate URL
     *
     * @return void
     */
    public function it_should_return_error_on_file_upload_on_s3_for_create_slider()
    {
        DB::setDefaultConnection('tenant');
        App\Models\Slider::where('deleted_at', '<>', '')->delete();
        $params = [
            'url' => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/test.png",
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];
        DB::setDefaultConnection('mysql');
        $this->post("slider", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Return error for file upload on update slider
     *
     * @return void
     */
    public function it_should_return_error_on_file_upload_on_s3_for_update_slider()
    {
        $params = [
            'url' => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/test.png",
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $connection = 'tenant';
        $slider = factory(\App\Models\Slider::class)->make();
        $slider->setConnection($connection);
        $slider->save();

        $this->patch("slider/".$slider->slider_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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

        App\Models\Slider::orderBy("slider_id", "DESC")->take(1)->delete();
        $slider->delete();
    }

    /**
     * @test
     *
     * Return error for invalid slider id on update slider
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_slider_id_on_update_slider()
    {
        $params = [
            'url' => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png",
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $this->patch("slider/".rand(1000000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
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
     * It should return error on slider update
     *
     * @return void
     */
    public function it_should_return_error_update_slider()
    {
        $params = [
            'url' => 'https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png',
            'sort_order' => "1",        
            'translations' =>  [
                [
                    'lang' => 'en',
                    'slider_title' => str_random(20),
                    'slider_description' => str_random(200)
                ]
            ],
        ];

        $this->patch("slider/".rand(500000000,8000000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
        
    }

}
