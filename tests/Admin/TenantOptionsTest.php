<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\TenantOption;

class TenantOptionsTest extends TestCase
{
    /**
     * @test
     *
     * Get all footer pages
     *
     * @return void
     */
    public function it_should_return_all_tenant_options()
    {
        /*$this->get(route('cms'), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "page_translations" => [
                        "*" => [
                            "page_id",
                            "language_id",
                            "title",
                            "description" => [
                                
                            ]
                        ]
                    ]
                ]
            ],
            "message"
        ]);*/
    }

    /**
     * @test
     *
     * No footer page found
     *
     * @return void
     */
    public function it_should_return_no_tenant_option_found()
    {
       /* $this->get(route("cms"), ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);*/
    }

    /**
     * @test
     *
     * Create footer page api
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
        $this->setConnection($connection);
        
        $slides = TenantOption::where("option_name", "slider")->get();
        dd($slides);

        $this->post("create_slider/", $params, ['Authorization' => 'Basic dGF0dmFzb2Z0X2FwaV9rZXk6dGF0dmFzb2Z0X2FwaV9zZWNyZXQ='])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
            ]);

       

        
        // TenantOption::where("option_name", "slider")->orderBy("tenant_option_id", "DESC")->take(1)->delete();
    }

}
