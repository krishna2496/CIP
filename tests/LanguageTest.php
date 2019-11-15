<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\Language;
use App\Models\TenantLanguage;
use App\Models\Tenant;

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
    public function it_should_return_language()
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
            "status" => "1"
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
    public function it_should_return_validation_error_for_create_language()
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
    public function it_should_return_validation_error_if_code_is_already_taken_for_create_language()
    {
        $language = Language::get()->random();

        $params = [        
            "name" => str_random(10),
            "code" => $language->code,
            "status" => "1"
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
    public function it_should_return_validation_error_if_code_is_invalid_for_create_language()
    {
        $params = [        
            "name" => str_random(10),
            "code" => str_random(10),
            "status" => "1"
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
    public function it_should_return_validation_error_if_status_is_invalid_for_create_language()
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

    /**
     * @test
     *
     * Edit language
     *
     * @return void
     */
    public function it_should_edit_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $this->patch("tenants/language/".$languageId, $params)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'language_id',
            ],
            'message',
            'status',
        ]);

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for edit language
     *
     * @return void
     */
    public function it_should_return_validation_error_for__edit_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $params = [        
            "name" => '',
            "code" => '',
            "status" => ''
        ];

        $this->patch("tenants/language/".$languageId, $params)
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for edit language
     *
     * @return void
     */
    public function it_should_return_validation_error_if_code_is_already_taken_for_edit_language()
    {
        $language = Language::get()->random();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $params = [        
            "name" => $languageName,
            "code" => $language->code,
            "status" => "1"
        ];

        $this->patch("tenants/language/".$languageId, $params)
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for edit language
     *
     * @return void
     */
    public function it_should_return_validation_error_if_code_is_invalid_for_edit_language()
    {
        $language = Language::get()->random();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $params = [        
            "name" => $languageName,
            "code" => str_random(10),
            "status" => "1"
        ];

        $this->patch("tenants/language/".$languageId, $params)
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for edit language
     *
     * @return void
     */
    public function it_should_return_validation_error_if_status_is_invalid_for_edit_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $params = [        
            "name" => $languageName,
            "status" => rand(1000, 50000)
        ];

        $this->patch("tenants/language/".$languageId, $params)
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return validation error for edit language
     *
     * @return void
     */
    public function it_should_return_validation_error_if_language_id_is_invalid_for_edit_language()
    {
        $languageId = rand(10000000, 50000000);
        $languageName = str_random(10);

        $params = [        
            "name" => $languageName,
            "status" => "1"
        ];

        $this->patch("tenants/language/".$languageId, $params)
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     * 
     * Delete language api
     * @return void
     */
    public function it_shoud_delete_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;

        $this->delete("tenants/language/".$languageId, [], [])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * Return language id not found on delete language api
     * @return void
     */
    public function it_shoud_return_no_language_id_found_errror_on_delete_language()
    {
        $languageId = rand(1000000, 5000000);

        $this->delete("tenants/language/".$languageId, [], [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Create tenant language
     *
     * @return void
     */
    public function it_should_create_tenant_language()
    {
        $tenant = factory(Tenant::class)->create();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "1"
        ];

        $this->post("tenants/tenant-language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        TenantLanguage::where("language_id", $languageId)->update(['deleted_at' => date("Y-m-d")]);

        // It should update tenant language
        $this->post("tenants/tenant-language", $params, [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "0"
        ];
        
        // It should update tenant language - To Verify & Validate : If user enter '0' in all language
        $this->post("tenants/tenant-language", $params, [])
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

        Language::where("name", $languageName)->delete();
        TenantLanguage::where("language_id", $languageId)->delete();
        $tenant->delete();
    }
    
    /**
     * @test
     *
     * Return invalid data error for create tenant language
     *
     * @return void
     */
    public function it_should_return_invalid_default_data__error_on_create_tenant_language()
    {
        $tenant = factory(Tenant::class)->create();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => rand(1000, 5000)
        ];

        $this->post("tenants/tenant-language", $params, [])
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

        Language::where("name", $languageName)->delete();
        $tenant->delete();
    }

    /**
     * @test
     *
     * Return invalid data error for create tenant language
     *
     * @return void
     */
    public function it_should_return_invalid_tenant_id_error_on_create_tenant_language()
    {
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => rand(10000000, 50000000),
            "language_id" => $languageId,
            "default" => "1"
        ];

        $this->post("tenants/tenant-language", $params, [])
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

        Language::where("name", $languageName)->delete();
    }

    /**
     * @test
     *
     * Return invalid data error for create tenant language
     *
     * @return void
     */
    public function it_should_return_invalid_language_id_error_on_create_tenant_language()
    {
        $tenant = factory(Tenant::class)->create();
        $languageId = rand(10000000, 50000000);
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "1"
        ];

        $this->post("tenants/tenant-language", $params, [])
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
        $tenant->delete();
    }

    /**
     * @test
     *
     * Return invalid data error for create tenant language
     *
     * @return void
     */
    public function it_should_return_invalid_data_error_on_create_tenant_language()
    {        
        $params = [        
            "tenant_id" => "",
            "language_id" => "",
            "default" => ""
        ];

        $this->post("tenants/tenant-language", $params, [])
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
     * Get tenant languages
     *
     * @return void
     */
    public function it_should_return_tenant_languages()
    {
        $tenant = factory(Tenant::class)->create();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "1"
        ];

        $this->post("tenants/tenant-language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $this->get('tenants/tenant-language/'.$tenant->tenant_id)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data',
            'message',
        ]);

        Language::where("name", $languageName)->delete();
        TenantLanguage::where("language_id", $languageId)->delete();
        $tenant->delete();
    }

    /**
     * @test
     *
     * Return no tenant found on get tenant languages
     *
     * @return void
     */
    public function it_should_return_no_tenant_found_on_get_tenant_languages()
    {
        $this->get('tenants/tenant-language/'.rand(10000000, 50000000))
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
     * Return invalid argument error on get tenant languages
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_on_get_tenant_languages()
    {
        $tenant = factory(Tenant::class)->create();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "1"
        ];

        $this->post("tenants/tenant-language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $this->get('tenants/tenant-language/'.$tenant->tenant_id."?order=test")
        ->seeStatusCode(400);

        Language::where("name", $languageName)->delete();
        TenantLanguage::where("language_id", $languageId)->delete();
        $tenant->delete();
    }

    /**
     * @test
     *
     * Delete tenant language
     *
     * @return void
     */
    public function it_should_delete_tenant_language()
    {
        $tenant = factory(Tenant::class)->create();
        $languageName = str_random(10);
        $params = [        
            "name" => $languageName,
            "code" => str_random(2),
            "status" => "1"
        ];

        $response = $this->post("tenants/language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $languageId = json_decode($response->response->getContent())->data->language_id;
        
        $params = [        
            "tenant_id" => $tenant->tenant_id,
            "language_id" => $languageId,
            "default" => "1"
        ];

        $response = $this->post("tenants/tenant-language", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);

        $tenantLanguageId = json_decode($response->response->getContent())->data->tenant_language_id;

        $this->delete("tenants/tenant-language/".$tenantLanguageId, [], [])
        ->seeStatusCode(204);

        // Return no tenant language found
        $this->delete("tenants/tenant-language/".$tenantLanguageId, [], [])
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

        TenantLanguage::where("language_id", $languageId)->delete();
        $tenant->delete();
    }
}
