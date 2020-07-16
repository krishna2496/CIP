<?php

namespace Tests\Unit\Repositories\Currency;

use App\Models\TenantCurrency;
use App\Models\Tenant;
use App\Repositories\Currency\Currency;
use App\Repositories\Currency\CurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use TestCase;
use Mockery;

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
     * @testdox Test store success
     *
     * @return void
     */
    public function testStoreSuccess()
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

        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        $tenant->shouldReceive('findOrFail')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('update')
            ->with(['default' => 0])
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('create')
            ->once()
            ->with($currencyData);

        $repository->store($request, $tenantId);
    }

    /**
     * @testdox Test update success
     *
     * @return void
     */
    public function testUpdateSuccess()
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

        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        $tenantCurrency->shouldReceive('where')
            ->twice()
            ->with(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('update')
            ->once()
            ->with(['default' => '0'])
            ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('update')
            ->once()
            ->with($currencyData)
            ->andReturn($tenantCurrency);

        $repository->update($request, $tenantId);
    }

    /**
     * @testdox Test get tenant currency list
     *
     * @return void
     */
    public function testGetTenantCurrencyListSuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);
        $repository = $this->getRepository(
            $tenantCurrency,
            $tenant
        );
        $data = ['perPage' => '10'];
        $request = new Request($data);
        $tenantId = 1;

        $tenant->shouldReceive('findOrFail')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        $tenantCurrency->shouldReceive('where')
             ->once()
             ->with(['tenant_id' => $tenantId])
             ->andReturn($tenantCurrency);

        $tenantCurrency->shouldReceive('orderBy')
             ->once()
             ->with('code', 'ASC')
             ->andReturn($tenantCurrency);

        $items = [
            'code'=> 'INR',
            'default'=> 1,
            'is_active'=> 1
        ];
        $mockTenantCurrencies = new LengthAwarePaginator($items, 0, 10, 1);
        $tenantCurrency->shouldReceive('paginate')
            ->once()
            ->with($data['perPage'])
            ->andReturn($mockTenantCurrencies);

        $tenantCurrencies = $repository->getTenantCurrencyList($request, $tenantId);
        $this->assertInstanceOf(LengthAwarePaginator::class, $tenantCurrencies);
        $this->assertSame($mockTenantCurrencies, $tenantCurrencies);
    }

    /**
     * @testdox Test get tenant currency list false
     *
     * @return void
     */
    public function testIsValidCurrencyFalse()
    {
        $tenant = $this->mock(Tenant::class);
        $tenantCurrency = $this->mock(TenantCurrency::class);

        $repository = $this->getRepository(
            $tenantCurrency,
            $tenant
        );

        $tenantId = 1;
        $data = [
            'code'=> 'FAK',
            'default'=> '1',
            'is_active'=> '1'
        ];
        $request = new Request($data);

        $isValid = $repository->isValidCurrency($request['code']);
        $this->assertEquals(false, $isValid);
    }

    /**
     * @testdox Test get tenant currency list
     *
     * @return void
     */
    public function testIsValidCurrencySuccess()
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

        $isValid = $repository->isValidCurrency($request['code']);
        $this->assertEquals(true, $isValid);
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
