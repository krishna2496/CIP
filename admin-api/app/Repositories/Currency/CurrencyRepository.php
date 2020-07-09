<?php

namespace App\Repositories\Currency;

use App\Repositories\Currency\Currency;
use Illuminate\Http\Request;
use App\Models\TenantCurrency;
use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class CurrencyRepository
{
    /**
     * @var App\Models\TenantCurrency
     */
    private $tenantCurrency;

    /**
     * @var App\Models\Tenant
     */
    private $tenant;

    /**
     * Create a new Currency repository instance.
     *
     * @param App\Models\TenantCurrency $tenantCurrency
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(TenantCurrency $tenantCurrency, Tenant $tenant)
    {
        $this->tenantCurrency = $tenantCurrency;
        $this->tenant = $tenant;
    }

    /**
     * Get list of all currency
     *
     * @return array
     */
    public function findAll()
    {
        return [
            new Currency('INR', '₹'),
            new Currency('EUR', '€'),
            new Currency('USD', '$'),
            new Currency('BRL', 'R$'),
            new Currency('ZWD', 'Z$'),
        ];
    }

    /**
     * Store currency
     *
     * @param Request $request
     * @param int $tenantId
     * @return void
     */
    public function store(Request $request, int $tenantId)
    {
        $tenant = $this->tenant->findOrFail($tenantId);

        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        if ($request['is_active'] === '1' && $request['default'] === '1') {
            $this->tenantCurrency->where('tenant_id', $tenantId)->update(['default' => '0']);
        }

        $this->tenantCurrency->create($currencyData);
    }

    /**
     * Update currency
     *
     * @param Request $request
     * @param int $tenantId
     * @return void
     */
    public function update(Request $request, int $tenantId)
    {
        $tenantCurrencyData = $this->tenantCurrency
            ->where(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->firstOrFail();

        $currencyData = [
            'tenant_id' => $tenantId,
            'code' => $request['code'],
            'default' => $request['default'],
            'is_active' => $request['is_active']
        ];

        if ($request['is_active'] === '1' && $request['default'] === '1') {
            $this->tenantCurrency->where('tenant_id', $tenantId)->update(['default' => '0']);
        }
        $this->tenantCurrency->where(['tenant_id' => $tenantId, 'code' => $request['code']])
            ->update($currencyData);
    }

    /**
     * List of all tenant currency
     *
     * @param array $request
     * @param int $tenantId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTenantCurrencyList(Request $request, int $tenantId) : LengthAwarePaginator
    {
        // Check tenant is present in the system
        $tenantData = $this->tenant->findOrFail($tenantId);

        $currencyTenantDetails = $this->tenantCurrency
            ->where(['tenant_id' => $tenantId])
            ->orderBy('code', 'ASC')
            ->paginate($request->perPage);
        return $currencyTenantDetails;
    }

    /**
     * Check request currency is available in currency list
     *
     * @param Request $request
     * @return boolean
     */
    public function isValidCurrency(Request $request) : bool
    {
        $allCurrencyList = $this->findAll();
        $allCurrencyArray = [];

        foreach ($allCurrencyList as $key => $value) {
            $code = $value->code;
            array_push($allCurrencyArray, $code);
        }

        if (!in_array($request['code'], $allCurrencyArray)) {
            return false;
        }

        return true;
    }
}
