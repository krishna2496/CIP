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
        
        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/policy/listing', ['token' => $token])
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
        
        $token = Helpers::getJwtToken($user->user_id);
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
        $policyPage = factory(\App\Models\PolicyPage::class)->make();
        $policyPage->setConnection($connection);
        $policyPage->save();

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $slug = $policyPage->slug;
        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/policy/'.$slug, ['token' => $token])
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
        $user->delete();
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

        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id);
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
        
        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/app/policy/listing', ['token' => $token])
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
        $user->delete();
        $policyPage->delete();
    }
}
