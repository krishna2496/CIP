<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\TenantCurrency;
use App\Models\Tenant;
use App\Repositories\Currency\Currency;

class CurrencyTest extends TestCase
{
    /**
     * @test
     *
     * List of tenant currency
     * @return void
     */
    public function it_should_list_all_tenant_currency()
    {
        $tenant = factory(Tenant::class)->create();

        for ($i=0; $i<3; $i++) {
            $tenantCurrency = factory(TenantCurrency::class, 5)->create(['tenant_id' => $tenant->tenant_id]);
        }

        $this->get('tenants/tenant-currency/'.$tenant->tenant_id)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data',
            'message',
        ]);

        App\Models\TenantCurrency::where(['tenant_id' => $tenant->tenant_id])->delete();
    }

    /**
     * @test
     *
     * Empty detail of tenant currency
     * @return void
     */
    public function it_should_return_with_empty_currency_data()
    {
        $tenant = factory(Tenant::class)->create();

        $this->get('tenants/tenant-currency/'.$tenant->tenant_id)
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        ;
    }

    /**
     * @test
     *
     * Error for invalid tenant id
     * @return void
     */
    public function it_should_return_invalid_tenant_id_error_on_listing_tenant_language()
    {
        $this->get('tenants/tenant-currency/'.rand(1000000, 2000000))
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
      * Return validation error of invalid code for create currency
      *
      * @return void
      */
    public function it_should_return_validation_error_if_code_is_invalid_for_create_language()
    {
        $tenant = factory(Tenant::class)->create();

        $params = [
            'currency' => [
                [
                    "code" => str_random(10),
                    "tenant_id" => $tenant->tenant_id,
                    "is_active" => '1'
                ],
            ],
        ];

        $this->post("tenants/tenant-currency", $params)
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
     * Return validation error of empty tenant id and code value for create currency
     *
     * @return void
     */
    public function it_should_return_validation_error_if_field_is_empty_for_create_language()
    {
        $params = [
            'currency' => [
                [
                    "code" => '',
                    "tenant_id" => '',
                    "is_active" => '1'
                ],
            ],
        ];

        $this->post("tenants/tenant-currency", $params)
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
     * Return validation error of invalid tenant id for create currency
     *
     * @return void
     */
    public function it_should_return_validation_error_if_tenant_id_is_invalid_for_create_language()
    {
        $params = [
            'currency' => [
                [
                    "code" => 'XYZ',
                    "tenant_id" => rand(1000000, 2000000),
                    "is_active" => '1'
                ],
            ],
        ];

        $this->post("tenants/tenant-currency", $params)
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
     * Return error if currency code is not available
     *
     * @return void
     */
    public function it_should_return_error_if_currency_code_is_not_available_in_system()
    {
        $tenant = factory(Tenant::class)->create();

        $params = [
            'currency' => [
                [
                    "code" => 'XYZ',
                    "tenant_id" => $tenant->tenant_id,
                    "is_active" => '1'
                ],
            ],
        ];

        $this->post("tenants/tenant-currency", $params)
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
     * Store currency for tenant
     *
     * @return void
     */
    public function it_should_store_currency_for_tenant()
    {
        $tenant = factory(Tenant::class)->create();
        $tenantCurrenctInstance = new App\Models\TenantCurrency;
        $tenantInstance = new App\Models\Tenant;
        $test = new App\Repositories\Currency\CurrencyRepository($tenantCurrenctInstance, $tenantInstance);
        $currencyData = $test->findAll();
        dd($currencyData);

        $params = [
            'currency' => [
                [
                    "code" => 'XYZ',
                    "tenant_id" => $tenant->tenant_id,
                    "is_active" => '1'
                ],
            ],
        ];

        $this->post("tenants/tenant-currency", $params)
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
