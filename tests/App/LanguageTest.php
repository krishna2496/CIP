<?php

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
        // Upload language file 

        //Get language file details
        $this->get('language/en', [])
        ->seeStatusCode(200)
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
     * Return error with invalid language code
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_laguage_code()
    {
        $this->get('language/eq', [])
        ->seeStatusCode(422)
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
