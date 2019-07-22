<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LanguageTest extends TestCase
{
    /**
     * @test
     *
     * Get language file 
     *
     * @return void
     */
    public function it_should_return_language_details_by_laguage_code()
    {
        $this->get('language/en', [])
          ->seeJsonStructure([
                "locale",
                "data" => [
                    "label",
                    "placeholder",
                    "errors"
                ]
            ]);
    }

    /**
     * @test
     *
     * Get language file with invalid language code
     *
     * @return void
     */
    public function it_should_return_language_details_by_invalid_laguage_code()
    {
        $this->get('language/eq', [])
          ->seeJsonStructure([
                "locale",
                "data" => [
                    "label",
                    "placeholder",
                    "errors"
                ]
            ]);
    }
}
