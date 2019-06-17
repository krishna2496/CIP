<?php

namespace App\Repositories\Tenant;
use App\Repositories\Tenant\TenantInterface;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Jobs\TenantDefaultLanguageJob;
use App\Jobs\TenantMigrationJob;
use App\Helpers\ResponseHelper;
use PDOException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    function __construct(Tenant $tenant) {
		$this->tenant = $tenant;
    }

    /**
     * Get listing of the tenants.
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function tenantList(Request $request)
    {
        try {
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
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
    }
	
    /**
     * Store a newly created resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
	public function store(Request $request)
    {
        try {
			$tenant = $this->tenant->create($request->toArray());
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
					$tenantOptionData['option_name'] = $option_name;
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
			
		} catch(PDOException $e) {
			throw new PDOException($e->getMessage());
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function find(int $id)
    {
        return $this->tenant->findTenant($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
		try {
			return $this->tenant->deleteTenant($id);			
		} catch(ModelNotFoundException $e){
			throw new ModelNotFoundException(trans('messages.custom_error_message.10004'));
        }
    }
	
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, int $id)
    {
        return $this->tenant->findTenant($id);
    }
}