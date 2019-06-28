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
            'name' => "testing_".str_random(10),
            'sponsor_id' => rand(1000,50000),
            'options' => [
                "theme_enabled" => 1,
                "skills_enabled" => 0
            ]
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
    }
}
