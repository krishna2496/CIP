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
}
