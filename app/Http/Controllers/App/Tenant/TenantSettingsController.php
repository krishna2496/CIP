<?php

namespace App\Http\Controllers\App\Tenant;

use App\Repositories\TenantSetting\TenantSettingRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Models\TenantSetting;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Validator;

class TenantSettingsController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var  App\Repositories\TenantSetting\tenantSettingRepository
     */
    private $tenantSettingRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param   App\Repositories\TenantSetting\TenantSettingRepository $tenantSettingRepository
     * @param   App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(TenantSettingRepository $tenantSettingRepository, ResponseHelper $responseHelper)
    {
        $this->tenantSettingRepository = $tenantSettingRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Fetch data from tenant setting data
        try {
            $tenantSettings = $this->tenantSettingRepository->fetchAllTenantSettings();
            $apiData = $tenantSettings->toArray();

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_SETTINGS_LISTING');
            $apiMessage = ($tenantSettings->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
            trans('messages.success.MESSAGE_TENANT_SETTINGS_LISTING');

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        }
    }
}
