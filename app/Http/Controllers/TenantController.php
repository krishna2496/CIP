<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Validator;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Http\Response;

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
        $tenantList = $this->tenant->tenantList($request);
		$responseMessage = (count($tenantList) > 0) ? trans('messages.success.MESSAGE_TENANT_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
		return ResponseHelper::success($this->response->status(), $responseMessage, $tenantList);
		
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
            // 'name' => 'required|unique:tenant',
            'name' => 'required',
            'sponsor_id'  => 'required',
        ]);

        if ($validator->fails()) {
			return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_422'), 
										trans('messages.status_type.HTTP_STATUS_TYPE_422'), 
										trans('messages.custom_error_code.ERROR_10001'), 
										$validator->errors()->first());
        }
		return $this->tenant->store($request);
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
    public function update(Request $request, int $id)
    {
		$tenant->update($request->all());
		try {
			$tenant = Tenant::findOrFail($id);
			
			
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
			$apiMessage = trans('messages.success.MESSAGE_TENANT_UPDATED');
			
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
		$tenant = $this->tenant->delete($id);
		
		// Set response data
		$apiStatus = app('Illuminate\Http\Response')->status();            
		$apiMessage = trans('messages.success.MESSAGE_TENANT_DELETED');

		return ResponseHelper::success($apiStatus, $apiMessage);
    }
}
