<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response, JsonResponse};
use App\Repositories\Tenant\TenantRepository;
use App\Helpers\ResponseHelper;
use App\Jobs\{TenantDefaultLanguageJob, TenantMigrationJob, CreateFolderInS3BucketJob};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator, PDOException;

class TenantController extends Controller
{
    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenant;
	
	/**
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new Tenant controller instance.
     *
     * @param  App\Repositories\Tenant\TenantRepository $tenant 
     * @return void
     */    
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
			return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $tenantList);
		} catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Store a newly created tenant into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request): JsonResponse
    {
		try {
            $validator = Validator::make($request->toArray(), [ 
				'name' => 'required|unique:tenant,name,NULL,tenant_id,deleted_at,NULL',
				'sponsor_id'  => 'required']);

            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_200001'),
                    $validator->errors()->first()
                );
            }

            $tenant = $this->tenant->store($request);
			
			// ONLY FOR DEVELOPMENT MODE. (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
			if (env('APP_ENV')=='local') {
				dispatch(new TenantDefaultLanguageJob($tenant));
			}
			
            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($tenant));

            // Create assets folder for tenant on AWS s3 bucket
            dispatch(new CreateFolderInS3BucketJob($tenant));

			// Set response data
            $apiStatus = trans('messages.status_code.HTTP_CREATED');
            $apiData = ['tenant_id' => $tenant->tenant_id];
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_CREATED');
			
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Show tenant details
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $tenantId): JsonResponse
    {
        // return  $this->tenant->find($tenantId);
		try {
            $tenantDetail = $this->tenant->find($tenantId);

            $apiStatus = $this->response->status();
            $apiData = $tenantDetail->toArray();
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_FOUND');
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return mixed
     */
    public function update(Request $request, int $id): JsonResponse
    {
       try {
            $rules = ['name' => 'unique:tenant,name,'. $id . ',tenant_id,deleted_at,NULL'];
            $validator = Validator::make($request->toArray(), $rules);

            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_200001'),
                    $validator->errors()->first()
                );
            }

            $tenant = $this->tenant->update($request, $id);
            
			$apiStatus = $this->response->status();
            $apiData = ['tenant_id' => $id];
            $apiMessage = trans('messages.success.MESSAGE_TENANT_UPDATED');

            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        } catch (PDOException $e) {
			$this->tenant->delete($tenant->tenant_id);
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            $this->tenant->delete($tenant->tenant_id);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function destroy(int $id)
    {
        try {
            $this->tenant->delete($id);
			
            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_TENANT_DELETED');

            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.200003'));
        }
    }
}
