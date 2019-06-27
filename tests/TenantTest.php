<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Tenant;

class TenantTest extends TestCase
{
    /**
     * Get all tenants list api
     *
     * @return void
     */
    public function testShouldReturnAllTenants()
    {
        $this->get("tenants", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
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
     * No tenant found
     */
    public function testShouldReturnNoTenantFound()
    {
        $this->get("tenants", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "status",
            "message"
        ]);
    }
    /**
     * Create tenant api
     */
    public function testShouldCreateTenant()
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
        $this->post("tenants", $params, []);
        $this->seeStatusCode(201);

        $this->seeJsonEquals([
            'status',
            'data' => [
              'tenant_id',
            ],
            'message',
            ]);

    }
    
    /**
     * @test
     * Get tenant details api
     */
    public function it_should_return_tenant_detail()
    {
        $tenant = Tenant::get()->random();
        $this->get(route("tenants.detail", ["tenant_id" => $tenant->tenant_id]), []);

        $this->seeStatusCode(200);
        
        $this->seeJsonStructure([
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
     * Delete tenant api
     */
    public function testShouldDeleteTenant()
    {
        $this->delete("tenants/1", [], []);
        $this->seeStatusCode(204);
    }

    /**
     * @test
     * Delete tenant api with already deleted or not available tenant id
     */
    public function it_should_return_tenant_not_found()
    {
        $this->delete("tenants/115", [], []);
        $this->seeStatusCode(404);
    }
}
