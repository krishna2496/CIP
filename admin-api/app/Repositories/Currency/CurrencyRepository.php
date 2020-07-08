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
     * Check request currency is available in currency list
     *
     * @param Request $request
     * @return boolean
     */
    public function checkAvailableCurrency(Request $request) : bool
    {
        $requestCurrency = $request->toArray();
        $allCurrencyList = $this->findAll();
        $allCurrencyArray = [];

        foreach ($allCurrencyList as $key => $value) {
            $code = $value->code;
            array_push($allCurrencyArray, $code);
        }

        foreach ($requestCurrency['currency'] as $key => $value) {
            if (!in_array($value['code'], $allCurrencyArray)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Store or update currency
     *
     * @param array $request
     * @param int $tenantId
     * @return array
     */
    public function storeOrUpdate(array $request)
    {
        foreach ($request['currency'] as $key => $value) {
            $condition = array('tenant_id' => $value['tenant_id'],
            'code' => $value['code']);
            $this->tenantCurrency->createOrUpdate($condition, $value);
        }
    }

    /**
     * List of all tenant currency
     *
     * @param array $request
     * @param int $tenantId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCurrencyDetails(Request $request, int $tenantId) : LengthAwarePaginator
    {
        // Check tenant is present in the system
        $tenantData = $this->tenant->findOrFail($tenantId);

        $currencyTenantDetails = $this->tenantCurrency->where(['tenant_id' => $tenantId])->orderBy('code', 'ASC')->paginate($request->perPage);
        return $currencyTenantDetails;
    }
}
