<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\FooterPage;

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

        $this->get('cms/'.$slug, [])
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

}
