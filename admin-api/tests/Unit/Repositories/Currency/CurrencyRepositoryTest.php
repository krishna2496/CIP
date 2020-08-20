<?php

namespace Tests\Unit\Repositories\Currency;

use App\Models\TenantCurrency;
use App\Models\Tenant;
use App\Repositories\Currency\CurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use TestCase;
use Mockery;
use App\Models\Currency;

class CurrencyRepositoryTest extends TestCase
{
    /**
     * @testdox Test findAll success
     *
     * @return void
     */
    public function testfindAllSuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);
        $repository = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $currencies = $repository->findAll();
        $this->assertIsArray($currencies);
    }

    /**
     * @testdox Test get tenant currency list false
     *
     * @return void
     */
    public function testIsAvailableCurrencyFalse()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $repository = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        $data = [
            'code'=> 'ABCD',
            'default'=> '1',
            'is_active'=> '1'
        ];

        $responseData = [
            false,
            'systemCurrencyInvalid' => false,
            'systemCurrency' => $data['code'],
        ];

        $request = new Request($data);
        $isValid = $repository->isAvailableCurrency($request['code']);
        $this->assertEquals($responseData, $isValid);
    }

    /**
     * @testdox Test get tenant currency list
     *
     * @return void
     */
    public function testIsAvailableCurrencySuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);


        $repository = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        $data = [
            'code'=> 'USD',
            'default'=> '1',
            'is_active'=> '1'
        ];
        $request = new Request($data);

        $responseData = [
            true,
            $data['code']
        ];

        $isValid = $repository->isAvailableCurrency($request['code']);        
        $this->assertEquals($responseData, $isValid);
    }

    /**
     * Create a new repository instance.
     *
     * @param  App\Models\Tenant $tenant
     * @param  App\Models\TenantCurrency $tenantCurrency
     * @return void
     */
    private function getRepository(
        TenantCurrency $tenantCurrency,
        Tenant $tenant
    ) {
        return new CurrencyRepository(
            $tenantCurrency,
            $tenant
        );
    }

    /**
     * Mock an object
     *
     * @param string name
     *
     * @return Mockery
     */
    private function mock($class)
    {
        return Mockery::mock($class);
    }
}
