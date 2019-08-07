<?php

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

        $this->get(route('policy.detail'), [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "pages" => [
                        "*" => [
                            "page_id",
                            "language_id",
                            "title",
                            "sections"
                        ]
                    ]
                ]
            ],
            "message"
        ]);
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
        $this->get(route("policy.detail"), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get all policy page list
     *
     * @return void
     */
    public function it_should_return_all_policy_pages_listing()
    {
        $connection = 'tenant';
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $this->get(route('policy.detail'), [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "pages" => [
                        "*" => [
                            "page_id",
                            "language_id",
                            "title"
                        ]
                    ]
                ]
            ],
            "message"
        ]);
        $policyPage->delete();
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
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $slug = $policyPage->slug;

        $this->get('/app/policy/'.$slug, [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "page_id",
                "slug",
                "status",
                "pages" => [
                    "*" => [
                        "page_id",
                        "language_id",
                        "title"
                    ]
                ]
            ],
            "message"
        ]);

        $policyPage->delete();
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

        $this->get('/app/policy/'.$slug, [])
        ->seeStatusCode(404)
        ->seeJsonStructure([
              "errors" => [
                  [
                    "status",
                    "message"
                  ]
              ]
        ]);
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
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $this->get('/app/policy/listing', [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "*" => [
                    "page_id",
                    "slug",
                    "status",
                    "pages" => [
                        "*" => [
                            "page_id",
                            "language_id",
                            "title",
                            "sections"
                        ]
                    ]
                ]
            ],
            "message"
        ]);

        $policyPage->delete();
    }
}
