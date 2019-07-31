<?php

class AppFooterPageTest extends TestCase
{
    /**
     * @test
     *
     * Get all footer pages
     *
     * @return void
     */
    public function it_should_return_all_footer_pages_with_details()
    {
        $this->get(route('cms.detail'), [])
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
    }

    /**
     * @test
     *
     * No footer_page found
     *
     * @return void
     */
    public function it_should_return_no_footer_page_found()
    {
        $this->get(route("cms.detail"), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     *
     * Get all footer page list
     *
     * @return void
     */
    public function it_should_return_all_footer_pages_listing()
    {
        $this->get(route('cms.detail'), [])
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
    }

    /**
     * @test
     *
     * Get footer page detail by slug
     *
     * @return void
     */
    public function it_should_return_footer_page_detail_by_slug()
    {
        $connection = 'tenant';
        $footer_page = factory(\App\Models\FooterPage::class)->make();
        $footer_page->setConnection($connection);
        $footer_page->save();

        $slug = $footer_page->slug;

        $this->get('/app/cms/'.$slug, [])
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

        $footer_page->delete();
    }

    /**
     * @test
     *
     * Get footer page detail by slug
     *
     * @return void
     */
    public function it_should_return_footer_page_data_detail_by_slug()
    {
        $connection = 'tenant';
        $footer_page = factory(\App\Models\FooterPage::class)->make();
        $footer_page->setConnection($connection);
        $footer_page->save();

        $slug = $footer_page->slug;

        $this->get('/app/cms/'.$slug, [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $footer_page->delete();
    }

    /**
     * @test
     *
     * Return error on invalid footer page detail by slug
     *
     * @return void
     */
    public function it_should_return_error_on_invalid_footer_page_detail_by_slug()
    {
        $slug = str_random(10) ;

        $this->get('/app/cms/'.$slug, [])
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
     * Return error on invalid footer page content by slug
     *
     * @return void
     */
    public function it_should_return_error_on_invalid_footer_page_by_slug()
    {
        $slug = str_random(10) ;

        $this->get('/app/cms/'.$slug, [])
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
}
