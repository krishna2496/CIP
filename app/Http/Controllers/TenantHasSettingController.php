<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\TenantHasSetting\TenantHasSettingRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use App\Repositories\Tenant\TenantRepository;
use App\Models\TenantHasSetting;

use Validator;

class TenantHasSettingController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\TenantHasSetting\TenantHasSettingRepository
     */
    private $tenantHasSettingRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;
    
    /**
     * Create a new Tenant has Setting controller instance.
     *
     * @param  App\Repositories\TenantHasSetting\TenantHasSettingRepository $tenantHasSettingRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantHasSettingRepository $tenantHasSettingRepository,
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantHasSettingRepository = $tenantHasSettingRepository;
        $this->tenantRepository = $tenantRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Show tenant Setting details
     *
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function show(int $tenantId): JsonResponse
    {
        try {
            $tenant = $this->tenantRepository->find($tenantId);
            $tenantSettingsData = $this->tenantHasSettingRepository->getSettingsList($tenantId);
            
            // Set response message
            $apiStatus = Response::HTTP_OK;
            $apiData = $tenantSettingsData->toArray();
            $apiMessage = (!empty($apiData)) ? trans('messages.success.MESSAGE_TENANT_SETTING_LISTING') :
            trans('messages.success.MESSAGE_NO_RECORD_FOUND');
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
     * Store a newly created tenant settings into database
     *
     * @param \Illuminate\Http\Request $request
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(Request $request, int $tenantId): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'settings' => 'required',
                'settings.*.tenant_setting_id' => 'required|exists:tenant_setting,tenant_setting_id',
                ]);

            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TENANT_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }
            // Check tenant is available or not
            $tenant = $this->tenantRepository->find($tenantId);

            $this->tenantHasSettingRepository->store($request, $tenantId);
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_SETTINGS_CREATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_INVALID_ARGUMENT'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }
}
