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
     * @test
     * 
     * Get all tenants list api, with search and order params
     * @return void
     */
    public function it_should_return_all_tenants_based_on_search_and_order()
    {
        $this->get("tenants?search=a&order=desc", [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' =>[ 
                '*' => [	
                    'name',
                    'sponsor_id',
                    'status',
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
     * @test
     * 
     * Get all tenants list api, with search and order params
     * @return void
     */
    public function it_should_return_exception_for_invalid_order_by()
    {
        $this->get("tenants?search=a&order=desct", [])
        ->seeStatusCode(400);        
    }
    
    /**
     * @test
     * 
     * No tenant found
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
     * @return void
     */
    public function it_should_create_tenant()
    {
        
        $params = [
            'name' => 'optimy'.rand(500, 1000),
            'sponsor_id' => '456123'
        ];
        
        $tenant = $this->post("tenants", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'tenant_id',
            ],
            'message',
            'status',
        ]);

        $tenantId = $tenant->response->getData()->data->tenant_id;
        $this->delete(route("tenants.destroy", ["tenant_id" => $tenantId]), [], [])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * Create tenant api, should return validation error
     * @return void
     */
    public function it_should_return_validation_error_on_create_tenant()
    {
        $params = [
            'name' => 'optimy   '.rand(500, 1000),
            'sponsor_id' => '456123'
        ];

        $tenant = $this->post("tenants", $params, [])
        ->seeStatusCode(422);
    }

    /**
     * @test
     * 
     * Get tenant details api
     * @return void
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
     * Get tenant details api, should return tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_on_tenant_detail()
    {
        $tenantId = rand(1000000000,20000000000);
        $this->get(route("tenants.detail", ["tenant_id" => $tenantId]), [])
        ->seeStatusCode(404);
    }
    
    /**
     * @test
     * 
     * Delete tenant api
     * @return void
     */
    public function it_shoud_delete_tenant()
    {
        // Create faker and delete it
        $tenant = factory(Tenant::class)->create();
        $this->delete(route("tenants.destroy", ["tenant_id" => $tenant->tenant_id]), [], [])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * Delete tenant api with already deleted or not available tenant id
     * @return void
     */
    public function it_should_return_tenant_not_found_on_delete()
    {
        $this->delete(route("tenants.destroy", ["tenant_id" => rand(99999999,999999999)]), [], [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * It will test update tenant api
     * @return void
     */
    public function it_should_update_tenant_data()
    {
        $tenant = factory(Tenant::class)->create();

        $data = [
            'name' => "testing".str_random(10),
            'sponsor_id' => rand(1000,50000)
        ];

        $this->patch(route("tenants.update", ["tenant_id" => $tenant->tenant_id]), $data)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' => [
                'tenant_id',
            ],
            'message',
            'status',
        ]);

        $this->assertTrue($tenant->delete());
    }

    /**
     * @test
     * 
     * It will return validation error, while updating tenant details
     * @return void
     */
    public function it_should_return_validation_error_for_update_tenant_data()
    {
        $tenant = factory(Tenant::class)->create();

        $data = [
            'name' => "testing ".str_random(10)
        ];

        $this->patch(route("tenants.update", ["tenant_id" => $tenant->tenant_id]), $data)
        ->seeStatusCode(422);

        $this->assertTrue($tenant->delete());
    }

    /**
     * @test
     * 
     * Tenant not found error, while update tenant api call with invalid tenant_id
     * @return void
     */
    public function it_should_return_tenant_not_found_on_update_tenant_data()
    {
        $tenantId = rand(500000000000,600000000000);

        $data = [
            'name' => "testing".str_random(10),
            'sponsor_id' => rand(1000,50000)
        ];

        $this->patch(route("tenants.update", ["tenant_id" => $tenantId]), $data)
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * It should run backgound process
     * @return void
     */
    public function it_run_tenant_create_background_process()
    {
        $params = [
            'name' => 'optimy'.rand(500, 1000),
            'sponsor_id' => '456123'
        ];
        
        $tenant = $this->post("tenants", $params, [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'tenant_id',
            ],
            'message',
            'status',
        ]);

        $tenantId = $tenant->response->getData()->data->tenant_id;
        
        $this->get("/tenant/runBackgroundProcess", [])
        ->seeStatusCode(200);

        
        $this->delete(route("tenants.destroy", ["tenant_id" => $tenantId]), [], [])
        ->seeStatusCode(204);
    }
}
