<?php
namespace App\Repositories\Tenant;

use App\Repositories\Tenant\TenantInterface;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantRepository implements TenantInterface
{
    /**
     * @var App\Models\Tenant
     */
    public $tenant;

    /**
     * Create a new Tenant repository instance.
     *
     * @param  App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Get listing of tenants
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function tenantList(Request $request): LengthAwarePaginator
    {
        $tenantQuery = $this->tenant->with('options', 'tenantLanguages', 'tenantLanguages.language');

        if ($request->has('search')) {
            $tenantQuery->where('name', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $tenantQuery->orderBy('tenant_id', $orderDirection);
        }

        return $tenantQuery->paginate(config('constants.PER_PAGE_LIMIT'));
    }

    /**
     * Store a newly created resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Tenant $tenant
     */
    public function store(Request $request): Tenant
    {
        $tenant = $this->tenant->create($request->toArray());

        // ONLY FOR DEVELOPMENT MODE. (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
        /* if (env('APP_ENV')=='local') {
            $apiUserData['api_key'] = $tenant->name.'_api_key';
            $apiUserData['api_secret'] = $tenant->name.'_api_secret';
            // Insert api_user data into table
            $tenant->apiUsers()->create($apiUserData);
        } */
        // ONLY FOR DEVELOPMENT MODE END

        // Add options data into `tenant_has_option` table
        if (isset($request->options) && count($request->options) > 0) {
            foreach ($request->options as $optionName => $optionValue) {
                $tenantOptionData['option_name'] = $optionName;
                $tenantOptionData['option_value'] = $optionValue;
                $tenant->options()->create($tenantOptionData);
            }
        }
        return $tenant;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return App\Models\Tenant $tenant
     */
    public function find(int $id): Tenant
    {
        return $this->tenant->findTenant($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->tenant->deleteTenant($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $requestArray
     * @param  int  $id
     * @return App\Models\Tenant $tenant
     */
    public function update(array $requestArray, int $id): Tenant
    {
        $tenant = $this->tenant->findOrFail($id);
        $tenant->update($requestArray);

        // Add options data into `tenant_has_option` table
        if (isset($requestArray['options']) && count($requestArray['options']) > 0) {
            foreach ($requestArray['options'] as $optionName => $optionValue) {
                $tenantOptionData['option_name'] = $optionName;
                $tenantOptionData['option_value'] = $optionValue;
                $tenant->options()->where('option_name', $optionName)
                    ->update($tenantOptionData);
            }
        }
        return $tenant;
    }
}
