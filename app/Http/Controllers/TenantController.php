<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ApiResponseController;
use App\Jobs\TenantMigrationJob;
use App\Tenant;
use Illuminate\Http\Request;
use Validator;

class TenantController extends ApiResponseController
{
    /**
     * Display a listing of the tenants.
     *
     * @return mixed
     */
    public function index()
    {
        // Get request parameter from URL
        $searchString = Input::get('search','');
        $orderType = Input::get('order','asc');

        // Create basic query for tenant list
        $tenantQuery = Tenant::with('options:tenant_option_id,tenant_id,option_name,option_value,created_at')->select('tenant_id','name','sponsor_id','created_at')
        ->whereNull('deleted_at');

        // Check if search parameter passed in URL then search parameter will search in name field of tenant table.
        if (!empty($searchString)) {
            $tenantQuery->where('name', 'like', '%' . $searchString . '%');
        }

        try {
            // Order by passed order or default order asc.
            $tenantList = $tenantQuery->orderBy('tenant_id',$orderType)->paginate(10);
        } catch(\Exception $e) { 
            // Catch database exception
            $this->errorType  = config('errors.code.10006');
            $this->apiErrorCode = 10006;
            $this->apiStatus  = 422;
            $this->apiMessage = $e->getMessage();
            return $this->errorResponse();
        }
        
        if (count($tenantList)>0) {
            // Set response data
            $this->apiData = $tenantList;
            $this->apiStatus = app('Illuminate\Http\Response')->status();
            $this->apiMessage = "Tenant listing successfully";
        } else {
            // Set response data                        
            $this->apiStatus = app('Illuminate\Http\Response')->status();
            $this->apiMessage = "No data found";
        }

        // Send API reponse
        return $this->response();
    }

    /**
     * Store a newly created tenant into database.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {        

        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'name' => 'required',
            'sponsor_id'  => 'required',
        ]);

        // If request parameter have any error
        if ($validator->fails()) {

            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_10001'), 
										$validator->errors()->first());
        }

        try {

            $createdTenant = Tenant::create($request->toArray());

            // ONLY FOR TESTING START Create api_user data (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
            if(env('APP_ENV')=='local'){
                $apiUserData['api_key'] = base64_encode($createdTenant->name.'_api_key');
                $apiUserData['api_secret'] = base64_encode($createdTenant->name.'_api_secret');
                // Insert api_user data into table
                $createdTenant->apiUsers()->create($apiUserData);
            }
            // ONLY FOR TESTING END
            
            // Add options data into `tenant_has_option` table            
            if (isset($request->options) && count($request->options) > 0) {
				foreach ($request->options as $option_name => $option_value) {
					$tenantOptionData['option_name'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $createdTenant->options()->create($tenantOptionData);
                }
            }

            // Set response data
            $this->apiStatus = app('Illuminate\Http\Response')->status();
            $this->apiData = ['tenant_id' => $createdTenant->tenant_id];
            $this->apiMessage = "Tenant created successfully";

            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($createdTenant));

            return $this->response();

        } catch (\Exception $e) {

            // If any error occure after create tenant record in database then need to remove it.
            $createdTenant->delete();

            // Error for duplicate tenant name, trying to store in database.
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                return $this->errorResponse(config('errors.status_code.HTTP_STATUS_400'), 
										config('errors.status_type.HTTP_STATUS_TYPE_400'), 
										config('errors.custom_error_code.ERROR_10002'), 
										config('errors.custom_error_message.10002'));
            } else { 
				// Any other error occured when trying to insert data into database for tenant.
                return $this->errorResponse(config('errors.status_code.HTTP_STATUS_400'), 
										config('errors.status_type.HTTP_STATUS_TYPE_400'), 
										config('errors.custom_error_code.ERROR_10006'), 
										config('errors.custom_error_message.10006'));
            }
        }
    }

    /**
     * Show tenant details
     *
     * @param int $id
     * @return mixed
     */
    public function show($tenant_id)
    {
        $tenantDetail = Tenant::with('options:tenant_option_id,tenant_id,option_name,option_value,created_at')
						->select('tenant_id','name','sponsor_id','created_at')
						->find($tenant_id);

        if ($tenantDetail) {
            $this->apiStatus = 200;
            $this->apiData = $tenantDetail;
			return $this->response();
        } else {
			return $this->errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
										config('errors.status_type.HTTP_STATUS_TYPE_403'), 
										config('errors.custom_error_code.ERROR_10004'), 
										config('errors.custom_error_message.10004'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
