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
use App\Helpers\DatabaseHelper;
use DB;
use App\Events\ActivityLogEvent;

//!  Tenanthassetting controller
/*!
This controller is responsible for handling tenanthassetting store/delete and show operations.
 */
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
     * @var App\Helpers\DatabaseHelper
     */
    private $databaseHelper;
    
    /**
     * Create a new Tenant has setting controller instance.
     *
     * @param  App\Repositories\TenantHasSetting\TenantHasSettingRepository $tenantHasSettingRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\DatabaseHelper $databaseHelper
     * @return void
     */
    public function __construct(
        TenantHasSettingRepository $tenantHasSettingRepository,
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper,
        DatabaseHelper $databaseHelper
    ) {
        $this->tenantHasSettingRepository = $tenantHasSettingRepository;
        $this->tenantRepository = $tenantRepository;
        $this->responseHelper = $responseHelper;
        $this->databaseHelper = $databaseHelper;
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
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
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
                'settings.*.tenant_setting_id' => 'required|exists:tenant_setting,tenant_setting_id,deleted_at,NULL',
                'settings.*.value' => 'required|in:0,1',
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

            // Store settings
            $this->tenantHasSettingRepository->store($request->toArray(), $tenantId);

            // Create connection with tenant database
            $this->databaseHelper->connectWithTenantDatabase($tenantId);

            // Store settings in admin tenant setting
            foreach ($request->settings as $value) {
                $tenantSettingId = $value['tenant_setting_id'];
                if ($value['value'] == 1) {
                    DB::table('tenant_setting')->updateOrInsert(['setting_id' => $value['tenant_setting_id']]);
                    $activityLogStatus = config('constants.activity_log_actions.ENABLED');
                } else {
                    DB::table('tenant_setting')->where(['setting_id' => $value['tenant_setting_id']])->delete();
                    $activityLogStatus = config('constants.activity_log_actions.DISABLED');
                }
            }
            
            // Disconnect tenant database and reconnect with default database
            DB::disconnect('tenant');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage =  trans('messages.success.MESSAGE_TENANT_SETTINGS_UPDATED');

            // Make activity log
            event(new ActivityLogEvent(
                config('constants.activity_log_types.TENANT_SETTINGS'),
                $activityLogStatus,
                get_class($this),
                $request->toArray(),
                $tenantSettingId
            ));
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
        }
    }
}
