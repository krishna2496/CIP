<?php

class CountryTest extends TestCase
{
    /**
     * @test
     *
     * Get country list
     *
     * @return void
     */
    public function it_should_return_all_country_list()
    {
        $this->get('/countries', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "*" => []
            ],
            "message"
        ]);
    }

    /**
     * @test
     *
     * No data found for Country
     *
     * @return void
     */
    public function it_should_return_no_country_found()
    {
        $this->get('/app/country', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
    }

    /**
     * @test
     *
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_authorization_token_for_get_country()
    {
        $this->get('/app/country', ['Authorization' => ''])
        ->seeStatusCode(401)
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
