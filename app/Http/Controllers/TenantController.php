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
use App\Jobs\CreateFolderInS3BucketJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use PDOException;
use InvalidArgumentException;
use Aws\S3\Exception\S3Exception;
use App\Repositories\ApiUser\ApiUserRepository;

class TenantController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;

    /**
     * @var App\Repositories\ApiUser\ApiUserRepository
     */
    private $apiUserRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Tenant controller instance.
     *
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @return void
     */
    public function __construct(
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper,
        ApiUserRepository $apiUserRepository
    ) {
        $this->tenantRepository = $tenantRepository;
        $this->responseHelper = $responseHelper;
        $this->apiUserRepository = $apiUserRepository;
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
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_INVALID_ARGUMENT'))
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
                'name' => 'required|unique:tenant,name,NULL,tenant_id,deleted_at,NULL',
                'sponsor_id'  => 'required']);

            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TENANT_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            $tenant = $this->tenantRepository->store($request);
            
            // ONLY FOR DEVELOPMENT MODE. (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
            if (env('APP_ENV')=='local' || env('APP_ENV')=='testing') {
                dispatch(new TenantDefaultLanguageJob($tenant));
            }
            
            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($tenant));

            // // Create assets folder for tenant on AWS s3 bucket
            dispatch(new CreateFolderInS3BucketJob($tenant));

            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiData = ['tenant_id' => $tenant->tenant_id];
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_CREATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (InvalidArgumentException $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_INVALID_ARGUMENT'))
            );
        } catch (S3Exception $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->s3Exception(
                config('constants.error_codes.FAILED_TO_CREATE_FOLDER_ON_S3'),
                trans('messages.custom_error_message.'.config('constants.error_codes.FAILED_TO_CREATE_FOLDER_ON_S3'))
            );
        } catch (\Exception $e) {
            // Delete created tenant
            $this->destroy($tenant->tenant_id);
            return $this->badRequest(trans('messages.custom_error_message.999999'));
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
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
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
            $rules = ['name' => 'sometimes|required|unique:tenant,name,'. $id . ',tenant_id,deleted_at,NULL'];
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

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
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

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Get all api users
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getAllApiUser(int $tenantId): JsonResponse
    {
        try {
            $tenantDetail = $this->tenantRepository->find($tenantId);

            $apiUsers = $this->apiUserRepository->apiUserList($tenantId);

            $responseMessage = (count($apiUsers) > 0) ? trans('messages.success.MESSAGE_TENANT_API_USER_LISTING') :
            trans('messages.success.MESSAGE_NO_RECORD_FOUND');

            return $this->responseHelper->successWithPagination($apiUsers, Response::HTTP_OK, $responseMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Get api user detail
     *
     * @param int $tenantId
     * @param int $apiUserId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getApiUserDetail(int $tenantId, int $apiUserId): JsonResponse
    {
        try {
            $tenantDetail = $this->tenantRepository->find($tenantId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        }
        try {
            $apiUser = $this->apiUserRepository->findApiUser($apiUserId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_API_USER_FOUND');
            $apiData = $apiUser->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_API_USER_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_API_USER_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Create api user for tenant
     *
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function createApiUser(int $tenantId): JsonResponse
    {
        try {
            $this->tenantRepository->find($tenantId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        }

        try {
            $apiKeys['api_key'] = str_random(16);
            $apiKeys['api_secret'] = str_random(16);
            $apiUser = $this->apiUserRepository->store($tenantId, $apiKeys);
            
            $response['api_user_id'] = $apiUser->api_user_id;
            $response['api_key'] = $apiUser->api_key;
            $response['api_secret'] = $apiKeys['api_secret'];
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_API_USER_CREATED_SUCCESSFULLY');

            return $this->responseHelper->success($apiStatus, $apiMessage, $response);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Renew api_secret for api user
     *
     * @param int $tenantId
     * @param int $apiUserId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function renewApiUser(int $tenantId, int $apiUserId): JsonResponse
    {
        try {
            $this->tenantRepository->find($tenantId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        }

        try {
            $apiSecret = str_random(16);
            
            $apiUser = $this->apiUserRepository->update($tenantId, $apiUserId, $apiSecret);
            $apiUser->api_secret = $apiSecret;

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_API_USER_UPDATED_SUCCESSFULLY');
            $apiData = $apiUser->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_API_USER_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_API_USER_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Delete api user
     *
     * @param int $tenantId
     * @param int $apiUserId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function deleteApiUser(int $tenantId, int $apiUserId): JsonResponse
    {
        try {
            $this->tenantRepository->find($tenantId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        }

        try {
            $apiUser = $this->apiUserRepository->delete($tenantId, $apiUserId);

            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_API_USER_DELETED');

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_API_USER_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_API_USER_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }
}
