<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;

class TenantHasSettings
{
    /**
     * @var App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;

     /**
     * Create a new Tenant has setting instance
     *
     * @param App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array $settings 
     * @return mixed
     */
    public function handle($request, Closure $next, ...$settings)
    {
        // Check for mission post/patch api
        $routeName = (!empty($request->route()[1])) ? $request->route()[1]['as'] : false;
        if ($routeName && ($routeName == 'missions.store' || $routeName == 'missions.update')) {
            $result = $this->missionSettingsPermissions($request, $request->get('mission_type'), $settings);
            if (!$result) {
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    '',
                    trans('messages.custom_error_message.ERROR_UNAUTHORIZED')
                );
            }
        }
        foreach ($settings as $key => $setting) {
            $result = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
                $setting,
                $request
            );
            if (!$result) {
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    '',
                    trans('messages.custom_error_message.ERROR_UNAUTHORIZED')
                );
            }
        }
        $response = $next($request);
        return $response;
    }

    public function missionSettingsPermissions($request, $type, $settings)
    {
        $volunteering = false;
        // check for volunteering settings
        if (in_array("volunteering", $settings)) {
            $result = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
                'volunteering',
                $request
            );
            $volunteering = true;
        }
        // check for goal settings
        if (($type == 'GOAL') & in_array("goal", $settings)) {
            $result = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
                'goal',
                $request
            );
            return ($result & $volunteering) ? true : false;
        }
        // check for time settings
        if (($type == 'TIME') & in_array("time", $settings)) {
            $result = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
                'time',
                $request
            );
            return ($result & $volunteering) ? true : false;
        }
        return false;
    }
}
