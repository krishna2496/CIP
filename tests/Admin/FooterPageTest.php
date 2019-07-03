<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\FooterPage;
use App\Models\FooterPagesLanguage;
use App\Repositories\User\UserRepository;

class FooterPageTest extends TestCase
{
    /**
     * @test
     *
     * Get all footer pages
     *
     * @return void
     */
    public function it_should_return_all_footer_pages()
    {
        $this->get(route('cms'), ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
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
        ]);
    }

    /**
     * @test
     *
     * No footer page found
     *
     * @return void
     */
    public function it_should_return_no_footer_page_found()
    {
        $this->get(route("cms"), 
        ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

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
                                'description' => str_random(255),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $this->post("cms/", $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
            ]);
        
        FooterPage::where('slug', $slug)->delete();
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
        $params = [
            'page_details' =>
                [
                'slug' => str_random(20),
                'translations' =>  [
                    [
                        'lang' => 'en',
                        'title' => str_random(20),
                        'sections' =>  [
                            [
                                'title' => str_random(20),
                                'description' => str_random(255),
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $connection = 'tenant';
        $footerPage = factory(\App\Models\FooterPage::class)->make();
        $footerPage->setConnection($connection);
        $footerPage->save();
        $page_id = $footerPage->page_id;

        $this->patch("cms/".$page_id, $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'page_id',
            ],
            'message',
            'status',
            ]);
        $footerPage->delete();
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
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
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
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
        )
        ->seeStatusCode(404);
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
                            'description' => str_random(255),
                        ],
                    ]
                ],
            ],
        ];
        
        $this->patch(
            "cms/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
        )
        ->seeStatusCode(404);
    }
}
