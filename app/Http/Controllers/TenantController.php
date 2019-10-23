<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\Tenant\TenantRepository;
use App\Helpers\ResponseHelper;
use App\Jobs\TenantDefaultLanguageJob;
use App\Jobs\TenantMigrationJob;
use App\Jobs\CompileScssFiles;
use App\Jobs\CreateFolderInS3BucketJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use PDOException;
use InvalidArgumentException;
use Aws\S3\Exception\S3Exception;
use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use Queue;
use App\Events\ActivityLogEvent;

class TenantController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Tenant controller instance.
     *
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantRepository = $tenantRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display a listing of the tenants.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tenantList = $this->tenantRepository->tenantList($request);
            
            $responseMessage = (count($tenantList) > 0) ? trans('messages.success.MESSAGE_TENANT_LISTING') :
            trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            return $this->responseHelper->successWithPagination($tenantList, Response::HTTP_OK, $responseMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        }
    }

    /**
     * Store a newly created tenant into database
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'name' => 'required|regex:/(^[A-Za-z0-9]+$)+/|
                max:512|unique:tenant,name,NULL,tenant_id,deleted_at,NULL',
                'sponsor_id'  => 'required|numeric']);

            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TENANT_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            $tenant = $this->tenantRepository->store($request);
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiData = ['tenant_id' => $tenant->tenant_id];
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_CREATED');

            // Make activity log
            event(new ActivityLogEvent(
                config('constants.activity_log_types.TENANT'),
                config('constants.activity_log_actions.CREATED'),
                get_class($this),
                $request->toArray(),
                $tenant->tenant_id
            ));
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (InvalidArgumentException $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (S3Exception $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->s3Exception(
                config('constants.error_codes.FAILED_TO_CREATE_FOLDER_ON_S3'),
                trans('messages.custom_error_message.FAILED_TO_CREATE_FOLDER_ON_S3')
            );
        } catch (\Exception $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Show tenant details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse;
     */
    public function show(int $tenantId): JsonResponse
    {
        try {
            $tenantDetail = $this->tenantRepository->find($tenantId);

            $apiStatus = Response::HTTP_OK;
            $apiData = $tenantDetail->toArray();
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_FOUND');

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse;
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $rules = [
                'name' => 'max:512|sometimes|regex:/(^[A-Za-z0-9]+$)+/|
                required|unique:tenant,name,'. $id . ',tenant_id,deleted_at,NULL',
                'sponsor_id' => 'sometimes|required|numeric'
            ];
            
            $validator = Validator::make($request->toArray(), $rules);

            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TENANT_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }
            $tenant = $this->tenantRepository->update($request->toArray(), $id);
            
            $apiStatus = Response::HTTP_OK;
            $apiData = ['tenant_id' => $id];
            $apiMessage = trans('messages.success.MESSAGE_TENANT_UPDATED');

             // Make activity log
            event(new ActivityLogEvent(
                config('constants.activity_log_types.TENANT'),
                config('constants.activity_log_actions.UPDATED'),
                get_class($this),
                $request->toArray(),
                $id
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse;
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->tenantRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_DELETED');

            // Make activity log
            event(new ActivityLogEvent(
                config('constants.activity_log_types.TENANT'),
                config('constants.activity_log_actions.DELETED'),
                get_class($this),
                [],
                $id
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
