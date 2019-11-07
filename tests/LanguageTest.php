<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\Language;

class LanguageTest extends TestCase
{
    /**
     * @test
     * 
     * List of all languages
     * @return void
     */
    public function it_should_list_all_languages()
    {        
        $this->get('tenants/language?search=&order=desc&status=true')
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data',
            'message',
        ]);
    }

    /**
     * @test
     * 
     * Return invalid argument error on list of all languages
     * @return void
     */
    public function it_should_return_invalid_argument_error_on_list_all_languages()
    {        
        $this->get('tenants/language?search=&order=test&status=true')
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
    }

    /**
     * @test
     * 
     * Return language
     * @return void
     */
    public function it_should_language()
    {        
        $language = Language::get()->random();
        $this->get('tenants/language/'.$language->language_id)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data',
            'message',
        ]);
    }

    /**
     * @test
     * 
     * Return no language found on get language
     * @return void
     */
    public function it_should_return_no_language_id_found_on_get_language()
    {        
        $this->get('tenants/language/'.rand(10000000, 50000000))
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Create language
     *
     * @return void
     */
    public function it_should_create_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => 1
        ];

        $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for create language
     *
     * @return void
     */
    public function it_should_reture_validation_error_for_create_language()
    {
        $params = [        
            "name" => '',
            "code" => '',
            "status" => ''
        ];

        $this->post("tenants/language", $params, [])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return validation error for create language
     *
     * @return void
     */
    public function it_should_reture_validation_error_if_code_is_already_taken_for_create_language()
    {
        $language = Language::get()->random();

        $params = [        
            "name" => str_random(10),
            "code" => $language->code,
            "status" => 1
        ];

        $this->post("tenants/language", $params, [])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return validation error for create language
     *
     * @return void
     */
    public function it_should_reture_validation_error_if_code_is_invalid_for_create_language()
    {
        $params = [        
            "name" => str_random(10),
            "code" => str_random(10),
            "status" => 1
        ];

        $this->post("tenants/language", $params, [])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return validation error for create language
     *
     * @return void
     */
    public function it_should_reture_validation_error_if_status_is_invalid_for_create_language()
    {
        $params = [        
            "name" => str_random(10),
            "code" => str_random(2),
            "status" => rand(1000, 5000)
        ];

        $this->post("tenants/language", $params, [])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }
}
