<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\Tenant;

class TenantTest extends TestCase
{
    /**
     * @test
     * 
     * Get all tenants list api
     *
     * @return void
     */
    public function it_should_return_all_tenants()
    {
        $this->get(route("tenants"), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' =>[ 
                '*' => [	
                    'name',
                    'sponsor_id',
                    'status',
                    'options' => [
                        '*' => [
                            'tenant_option_id',
                            'option_name',
                            'option_value'
                        ]
                    ],
                    'tenant_languages' => [
                        '*' => [
                            'language_id',
                            'default'
                        ]
                    ]
                ]
			],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'total_pages',
                'next_url',
            ]
        ]);
    }
    
    /**
     * 
     * 
     * No tenant found
     * 
     * @return void
     */
    public function it_should_return_no_tenant_found()
    {
        $this->get(route("tenants"), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }

    /**
     * @test
     * 
     * Create tenant api
     * 
     * @return void
     */
    public function it_should_create_tenant()
    {
        $params = [
            'name' => 'tatva_'.rand(500, 1000),
            'sponsor_id' => '456123',
            'options' => 
            [
              'theme_enabled' => '1',
              'skills_enabled' => '1',
            ],
        ];

        $this->post("tenants", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'tenant_id',
            ],
            'message',
            'status',
            ]);
    }


    /**
     *
     * 
     * Create tenant api, throw S3exception
     * 
     * @return void
     */
    public function it_errors_should_throw_s3_exception_on_tenant_create()
    {
        // $this->expectException(S3Exception::class);
        // $this->expectExceptionCode(500);

        $params = [
            'name' => 'tatva_'.rand(500, 1000),
            'sponsor_id' => '456123',
            'options' => 
            [
              'theme_enabled' => '1',
              'skills_enabled' => '1',
            ],
        ];

        $this->post(route("tenants"), $params, [])
        ->seeStatusCode(500);
    }

    /**
     * @test
     * Get tenant details api
     */
    public function it_should_return_tenant_detail()
    {
        $tenant = Tenant::get()->random();
        $this->get(route("tenants.detail", ["tenant_id" => $tenant->tenant_id]), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' =>[
                'name',
                'sponsor_id',
                'status',
                'options' => [
                    '*' => [
                        'tenant_option_id',
                        'option_name',
                        'option_value'
                    ]
                ],
                'tenant_languages' => [
                    '*' => [
                        'language_id',
                        'default'
                    ]
                ]
			]
        ]);
    }
    
    /**
     * @test
     * 
     * Delete tenant api
     * 
     * @return void
     */
    public function it_shoud_delete_tenant()
    {
        $tenant = Tenant::get()->random();
        $this->delete(route("tenants.destroy", ["tenant_id" => $tenant->tenant_id]), [], [])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * Delete tenant api with already deleted or not available tenant id
     * 
     * @return void
     */
    public function it_errors_should_return_tenant_not_found_on_delete()
    {
        $this->delete(route("tenants.destroy", ["tenant_id" => rand(10000,50000)]), [], [])
        ->seeStatusCode(404);
    }
}
