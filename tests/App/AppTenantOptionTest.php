<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AppTenantOptionTest extends TestCase
{
    /**
     * @test
     *
     * Get all tenant option
     *
     * @return void
     */
    public function it_should_return_all_tenant_options()
    {
        $this->get(route('connect'), [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" =>  [
                "defaultLanguage",
                "defaultLanguageId",
                "language"
            ]
        ]);
    }
}
