<?php

namespace App\Helpers;

use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use Illuminate\Http\Request;

class TenantSettingHelper
{
    /**
     * @var App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;

    /**
     * Constructor
     * @param App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     * @return TenantSettingHelper
     */
    public function __construct(
        TenantActivatedSettingRepository $tenantActivatedSettingRepository
    ) {
        $this->tenantActivatedSettingRepository = $tenantActivatedSettingRepository;
    }

    /**
     * Get available mission types based on activated tenant settings
     *
     * @param Request $request
     * @return $missionTypes
     */
    public function getAvailableMissionTypes(Request $request): array {
        $activatedTenantSettings = $this->tenantActivatedSettingRepository
            ->getAllTenantActivatedSetting($request);

        $missionTypeSettingsMap = [
            config('constants.mission_type.GOAL') => config('constants.tenant_settings.VOLUNTEERING_GOAL_MISSION'),
            config('constants.mission_type.TIME') => config('constants.tenant_settings.VOLUNTEERING_TIME_MISSION')
        ];

        $missionTypes = [];
        foreach ($missionTypeSettingsMap as $missionType => $setting) {
            if (in_array($setting, $activatedTenantSettings)) {
                $missionTypes[] = $missionType;
            }
        }
        return $missionTypes;
    }

    /**
     * Check if required tenant setting based on mission type is enabled
     *
     * @param Request $request
     * @param string $missionType if not provided, mission type from the request will be used
     * @return bool
     */
    private function isRequiredSettingForMissionTypeEnabled(
        Request $request,
        string $missionType = null
    ) : bool {

        $tenantSetting = null;
        $missionType = $missionType ?? $request->get('mission_type');
        switch ($missionType) {
            case config('constants.mission_type.GOAL'):
                $tenantSetting = config('constants.tenant_settings.VOLUNTEERING_GOAL_MISSION');
                break;
            case config('constants.mission_type.TIME'):
                $tenantSetting = config('constants.tenant_settings.VOLUNTEERING_TIME_MISSION');
                break;
        }

        return $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
            $tenantSetting,
            $request
        );
    }
}
