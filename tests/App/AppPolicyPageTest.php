<?php
use App\Helpers\Helpers;

class AppPolicyPageTest extends TestCase
{
    /**
     * @test
     *
     * Get all policy pages detail
     *
     * @return void
     */
    public function it_should_return_all_policy_pages_with_details()
    {
        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/listing', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $policyPage->delete();
    }

    /**
     * @test
     *
     * No policy_page found
     *
     * @return void
     */
    public function it_should_return_no_policy_page_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/listing', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get policy page detail by slug
     *
     * @return void
     */
    public function it_should_return_policy_page_detail_by_slug()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
     
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

        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/'.$slug, ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "page_id",
                "slug",
                "status",
                "pages" => [
                    [
                        "sections"
                    ]
                ]
            ],
            "message"
        ]);
        $user->delete();
        App\Models\PolicyPage::where('slug', $slug)->delete();
    }

    /**
     * @test
     *
     * Return error on invalid policy page detail by slug
     *
     * @return void
     */
    public function it_should_return_error_on_invalid_policy_page_detail_by_slug()
    {
        $slug = str_random(10) ;

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/'.$slug, ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
              "errors" => [
                  [
                    "status",
                    "message"
                  ]
              ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get policy page list
     *
     * @return void
     */
    public function it_should_return_policy_page_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/listing', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "pages"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $policyPage->delete();
    }

    /**
     * @test
     *
     * Return invalid argument error on get policy page listing
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_on_policy_page_listing()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();        

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/listing?order=test', ['token' => $token])
          ->seeStatusCode(500)
          ->seeJsonStructure([
              "errors" => [
                  [
                    "status",
                    "type",
                    "message"
                  ]
              ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Get policy page list
     *
     * @return void
     */
    public function it_should_return_policy_page_list_with_other_language()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/policy/listing', ['token' => $token, 'X-localization' => 'fr'])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "pages"
                ]
            ],
            "message"
        ]);
        $user->delete();
        $policyPage->delete();
    }
}
