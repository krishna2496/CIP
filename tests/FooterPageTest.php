<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class FooterPageTest extends TestCase
{
    /**
     * @test
     * 
     * Get all footer pages
     *
     * @return void
     */
    public function it_should_return_all_footer_pages()
    {        
        // when this user tries to create a new book
        $this->call("GET", "cms", [],[], [], [
            "HTTP_Authorization" => "Basic " . base64_encode("tatvasoft_api_key:tatvasoft_api_secret"),
            "PHP_AUTH_USER" => "tatvasoft_api_key", // must add this header since PHP won't set it correctly
            "PHP_AUTH_PW" => "tatvasoft_api_secret" // must add this header since PHP won't set it correctly as well
        ]);
    

        // $this->get("cms", [], $headers);
        $this->seeStatusCode(200);
       
        $this->seeJsonStructure([
            "status",
            'data',
            "message"
        ]);
    }

}
