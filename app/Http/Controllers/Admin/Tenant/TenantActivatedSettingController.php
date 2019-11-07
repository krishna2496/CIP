<?php

namespace App\Http\Controllers\Admin\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use App\Models\TenantActivatedSetting;
use Validator;
use App\Events\User\UserActivityLogEvent;

class TenantActivatedSettingController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var  App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository $tenantActivatedSettingRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantActivatedSettingRepository = $tenantActivatedSettingRepository;
        $this->responseHelper = $responseHelper;
    }

       
    /**
     * Store a newly created tenant activated settings into database
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'settings' => 'required',
            'settings.*.tenant_setting_id' => 'required|exists:tenant_setting,tenant_setting_id,deleted_at,NULL',
            'settings.*.value' => 'required|in:0,1',
            ]);

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_SETTING_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        // Store settings
        $this->tenantActivatedSettingRepository->store($request->toArray());

        $requestArray = $request->toArray();

        foreach ($requestArray['settings'] as $requestData) {
            $activityLogStatus = $requestData['value'] == 1 ?
                config('constants.activity_log_actions.CREATED') : config('constants.activity_log_actions.DELETED');

            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.TENANT_SETTINGS'),
                $activityLogStatus,
                config('constants.activity_log_user_types.API'),
                $request->header('php-auth-user'),
                get_class($this),
                $request->toArray(),
                null,
                $requestData['tenant_setting_id']
            ));
        }
       
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage =  trans('messages.success.MESSAGE_TENANT_SETTINGS_UPDATED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
