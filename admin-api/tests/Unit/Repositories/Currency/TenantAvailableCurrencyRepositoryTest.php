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
use App\Repositories\Currency\TenantAvailableCurrencyRepository;
use App\Models\TenantAvailableCurrency;

class TenantAvailableCurrencyRepositoryTest extends TestCase
{
    /**
     * @testdox Test store success
     *
     * @return void
     */
    public function testStoreRepositorySuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $tenantAvailableCurrency = $this->mock(TenantAvailableCurrency::class);
        $repository = $this->getRepository(
            $tenantAvailableCurrency,
            $tenant,
            $currencyRepository
        );
        $tenantId = 1;
        $data = [
            'code'=> 'USD',
            'default'=> '1',
            'is_active'=> '1'
        ];
        $request = $data;

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

        $tenantAvailableCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('update')
            ->with(['default' => 0])
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('create')
            ->once()
            ->with($currencyData);

        $repository->store($request, $tenantId);
    }

    /**
     * @testdox Test update success
     *
     * @return void
     */
    public function testUpdateRepositorySuccess()
    {
        $tenant = $this->mock(Tenant::class);
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $tenantAvailableCurrency = $this->mock(TenantAvailableCurrency::class);
        $repository = $this->getRepository(
            $tenantAvailableCurrency,
            $tenant,
            $currencyRepository
        );

        $tenantId = 1;
        $data = [
            'code'=> 'USD',
            'default'=> '1',
            'is_active'=> '1'
        ];
        $request = $data;

        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        $tenantAvailableCurrency->shouldReceive('where')
            ->twice()
            ->with(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('where')
            ->once()
            ->with('tenant_id', $tenantId)
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('update')
            ->once()
            ->with(['default' => '0'])
            ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('update')
            ->once()
            ->with($currencyData)
            ->andReturn($tenantAvailableCurrency);

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
        $currencyRepository = $this->mock(CurrencyRepository::class);
        $tenantAvailableCurrency = $this->mock(TenantAvailableCurrency::class);
        $repository = $this->getRepository(
            $tenantAvailableCurrency,
            $tenant,
            $currencyRepository,
        );
        $data = ['perPage' => '10'];
        $perPage = 10;
        $request = new Request($data);
        $tenantId = 1;

        $tenant->shouldReceive('findOrFail')
            ->once()
            ->with($tenantId)
            ->andReturn($tenant);

        $tenantAvailableCurrency->shouldReceive('where')
             ->once()
             ->with(['tenant_id' => $tenantId])
             ->andReturn($tenantAvailableCurrency);

        $tenantAvailableCurrency->shouldReceive('orderBy')
             ->once()
             ->with('code', 'ASC')
             ->andReturn($tenantAvailableCurrency);

        $items = [
            'code'=> 'INR',
            'default'=> 1,
            'is_active'=> 1
        ];
        $mockTenantCurrencies = new LengthAwarePaginator($items, 0, 10, 1);
        $tenantAvailableCurrency->shouldReceive('paginate')
            ->once()
            ->with($data['perPage'])
            ->andReturn($mockTenantCurrencies);

        $tenantCurrencies = $repository->getTenantCurrencyList(10, $tenantId);
        $this->assertInstanceOf(LengthAwarePaginator::class, $tenantCurrencies);
        $this->assertSame($mockTenantCurrencies, $tenantCurrencies);
    }

    /**
     * Create a new repository instance.
     *
     * @param App\Models\TenantAvailableCurrency $tenantAvailableCurrency
     * @param App\Models\Tenant $tenant
     * @param App\Repositories\Currency\CurrencyRepository $currencyRepository
     * @return void
     */
    private function getRepository(
        TenantAvailableCurrency $tenantAvailableCurrency,
        Tenant $tenant,
        CurrencyRepository $currencyRepository
    ) {
        return new TenantAvailableCurrencyRepository(
            $tenantAvailableCurrency,
            $tenant,
            $currencyRepository
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
