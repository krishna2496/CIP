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
        $connection = 'tenant';
        $footer_page = factory(\App\Models\FooterPage::class)->make();
        $footer_page->setConnection($connection);
        $footer_page->save();

        $this->get(route('app.cms.detail'), [])
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
        $footer_page->delete();
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
        $this->get(route("app.cms.detail"), [])
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
        $connection = 'tenant';
        $footer_page = factory(\App\Models\FooterPage::class)->make();
        $footer_page->setConnection($connection);
        $footer_page->save();

        $this->get(route('app.cms.listing'), [])
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
        $footer_page->delete();
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
                "pages"
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
    
    /**
     * @test
     *
     * No footer_page found
     *
     * @return void
     */
    public function it_should_return_no_footer_page_list_found()
    {
        $this->get(route("app.cms.listing"), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
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
        $this->get('app/cms/listing?order=test', [])
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
}
