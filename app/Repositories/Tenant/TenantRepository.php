<?php

namespace App\Repositories\Tenant;
use App\Repositories\Tenant\TenantInterface;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Jobs\TenantDefaultLanguageJob;
use App\Jobs\TenantMigrationJob;
use App\Helpers\ResponseHelper;

class TenantRepository implements TenantInterface
{
    public $tenant;

    function __construct(Tenant $tenant) {
		$this->tenant = $tenant;
    }


    public function tenantList(Request $request)
    {
        $tenantQuery = $this->tenant->with('options', 'tenantLanguages', 'tenantLanguages.language');
        
		if ($request->has('search')) {
			$tenantQuery->where('name', 'like', '%' . $request->input('search') . '%');
		}
		if ($request->has('order')) {
			$orderDirection = $request->input('order','asc');
			$tenantQuery->orderBy('tenant_id', $orderDirection);
		}
        
		$tenantList = $tenantQuery->paginate(config('constants.PER_PAGE_LIMIT'));
		
		return $tenantList;
    }
	
	public function store(Request $request)
    {
        try {
			$tenant = $this->tenant->create($request->toArray());
			// dd($tenant);
			dispatch(new TenantDefaultLanguageJob($tenant));
			
			 // ONLY FOR TESTING START Create api_user data (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
            if(env('APP_ENV')=='local'){
                $apiUserData['api_key'] = base64_encode($tenant->name.'_api_key');
                $apiUserData['api_secret'] = base64_encode($tenant->name.'_api_secret');
                // Insert api_user data into table
                $tenant->apiUsers()->create($apiUserData);
            }
            // ONLY FOR TESTING END
            
            // Add options data into `tenant_has_option` table            
            if (isset($request->options) && count($request->options) > 0) {
				foreach ($request->options as $option_name => $option_value) {
					$tenantOptionData['option_name_'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $tenant->options()->create($tenantOptionData);
                }
            }

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiData = ['tenant_id' => $tenant->tenant_id];
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_CREATED');

            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($tenant));
			
			return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
			
		} catch(\Exception $e) {
			dd($e);
		}
    }

    public function find($id)
    {
        return $this->tenant->findTenant($id);
    }


    public function delete($id)
    {
        return $this->tenant->deleteTenant($id);
    }
}