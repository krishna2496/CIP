<?php

namespace App\Http\Controllers\Admin\Tenant;

use App\Repositories\TenantSetting\TenantSettingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Models\TenantSetting;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Validator;
use App\Helpers\Helpers;

class TenantSettingsController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var  App\Repositories\TenantSetting\TenantSettingRepository
     */
    private $tenantSettingRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\TenantSetting\TenantSettingRepository $tenantSettingRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        TenantSettingRepository $tenantSettingRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers
    ) {
        $this->tenantSettingRepository = $tenantSettingRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
    }

    /**
     * Display a listing of tenant settings.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Fetch all tenant settings details from super admin
        $getTenantSettings = $this->helpers->getAllTenantSetting($request);

        // Fetch all tenant settings data
        $tenantSettings = $this->tenantSettingRepository->fetchAllTenantSettings();
        // dd($getTenantSettings);
        $tenantSettingData = array();

        if ($tenantSettings->count() &&  $getTenantSettings->count()) {
            foreach ($tenantSettings as $settingKey => $tenantSetting) {
                $index = $getTenantSettings->search(function ($value, $key) use ($tenantSetting) {
                    return $value->tenant_setting_id == $tenantSetting->setting_id;
                });
                
                $tenantSettingData[$index]['tenant_setting_id'] = $tenantSettings[$index]
                ->tenant_setting_id;
                $tenantSettingData[$index]['key'] = $getTenantSettings[$index]->key;
                $tenantSettingData[$index]['description'] = $getTenantSettings[$index]
                ->description;
                $tenantSettingData[$index]['title'] = $getTenantSettings[$index]
                ->title;
            }
        }
        $apiData = $tenantSettingData;

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($tenantSettings->isEmpty() || $getTenantSettings->isEmpty()) ?
        trans('messages.success.MESSAGE_NO_RECORD_FOUND'):
        trans('messages.success.MESSAGE_TENANT_SETTINGS_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $settingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $settingId): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "value" => "required|in:1,0",
                ]
            );
            
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TENANT_SETTING_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }
            
            $setting = $this->tenantSettingRepository->updateSetting($request->toArray(), $settingId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_SETTING_UPDATE_SUCCESSFULLY');
            $apiData = ['tenant_setting_id' => $setting->tenant_setting_id];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SETTING_FOUND'),
                trans('messages.custom_error_message.ERROR_SETTING_FOUND')
            );
        }
    }
}
