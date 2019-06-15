<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;


use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Validator;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TenantController extends Controller
{
	private $tenant;
	private $response;
	
	public function __construct(TenantRepository $tenant, Response $response)
    {
        $this->tenant = $tenant;
		$this->response = $response;
    }
	
    /**
     * Display a listing of the tenants.
     *
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
			$tenantList = $this->tenant->tenantList($request);
			$responseMessage = (count($tenantList) > 0) ? trans('messages.success.MESSAGE_TENANT_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
			return ResponseHelper::success($this->response->status(), $responseMessage, $tenantList);
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

    /**
     * Store a newly created tenant into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
		// Server side validataions
        $validator = Validator::make($request->toArray(), [
            'name' => 'required|unique:tenant',
            'sponsor_id'  => 'required',
        ]);

        // If request parameter have any error
        if ($validator->fails()) {

            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_422'), 
										trans('messages.status_type.HTTP_STATUS_TYPE_422'), 
										trans('messages.custom_error_code.ERROR_10001'), 
										$validator->errors()->first());
        }

        try {

            return $this->tenant->store($request);

            /* // Store default languages
            dispatch(new TenantDefaultLanguageJob($createdTenant));
            
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
					$tenantOptionData['option_name_'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $createdTenant->options()->create($tenantOptionData);
                }
            }

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiData = ['tenant_id' => $createdTenant->tenant_id];
            $apiMessage =  trans('api_success_messages.success.MESSAGE_TENANT_CREATED');

            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($createdTenant));

            return ResponseHelper::response($apiStatus, $apiMessage, $apiData); */

        } catch (\Exception $e) {

            // If any error occure after create tenant record in database then need to remove it.
            // $createdTenant->delete();

				// Any other error occured when trying to insert data into database for tenant.
                return ResponseHelper::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_400'), 
										trans('api_error_messages.status_type.HTTP_STATUS_TYPE_400'), 
										trans('api_error_messages.custom_error_code.ERROR_10006'), 
										trans('api_error_messages.custom_error_message.10006'));
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
        $tenantDetail = Tenant::with('options:tenant_option_id,tenant_id,option_name,option_value,created_at','languages:tenant_language_id,tenant_id,language_id,default','languages.language:language_id,code')
						->select('tenant_id','name','sponsor_id','created_at')
                        ->whereNull('deleted_at')
						->find($tenant_id);

        if ($tenantDetail) {
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiData = $tenantDetail;
			return Helpers::response($apiStatus, '', $apiData);
        } else {
			return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_403'), 
										trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
										trans('api_error_messages.custom_error_code.ERROR_10004'), 
										trans('api_error_messages.custom_error_message.10004'));
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
		try {
			$tenant = Tenant::findOrFail($id);
			$tenant->update($request->all());
			
			// Tenent options data
			// Add options data into `tenant_has_option` table            
            if (isset($request->options) && count($request->options) > 0) {
				foreach ($request->options as $option_name => $option_value) {
					$tenantOptionData['option_name'] = $option_name;
                    $tenantOptionData['option_value'] = $option_value;
                    $tenant->options()->update($tenantOptionData);
                }
            }
			$apiStatus = app('Illuminate\Http\Response')->status();
            $apiData = ['tenant_id' => $id];
			$apiMessage = trans('api_success_messages.success.MESSAGE_TENANT_UPDATED');
			
			return Helpers::response($apiStatus, $apiMessage, $apiData);
		}
		catch(\Exception $e) {
			return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_404'), 
										trans('api_error_messages.status_type.HTTP_STATUS_TYPE_404'), 
										trans('api_error_messages.custom_error_code.ERROR_10004'), 
										trans('api_error_messages.custom_error_message.10004'));
        }		
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
			$tenant = $this->tenant->delete($id);
            
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();            
            $apiMessage = trans('api_success_messages.success.MESSAGE_TENANT_DELETED');

            return ResponseHelper::success($apiStatus, $apiMessage);

        } catch(ModelNotFoundException $e){
			throw new ModelNotFoundException(trans('api_error_messages.custom_error_message.10004'));
        }
    }
}
