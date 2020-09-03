<?php
namespace App\Repositories\TenantActivatedSetting;

use App\Helpers\Helpers;
use App\Models\TenantActivatedSetting;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TenantActivatedSettingRepository implements TenantActivatedSettingInterface
{
    /**
     * The tenantActivatedSetting for the model.
     *
     * @var App\Models\TenantActivatedSetting
     */
    public $tenantActivatedSetting;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\TenantActivatedSetting $tenantActivatedSetting
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(TenantActivatedSetting $tenantActivatedSetting, Helpers $helpers)
    {
        $this->tenantActivatedSetting = $tenantActivatedSetting;
        $this->helpers = $helpers;
    }

    /**
     * Fetch tenant settings with specified keys
     *
     * @param $ids
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getList(array $ids = []): Collection
    {
        return $this->tenantActivatedSetting
            ->select(
                'ts.setting_id',
                'ts.tenant_setting_id'
            )
            ->join('tenant_setting AS ts', 'ts.tenant_setting_id', '=', 'tenant_activated_setting.tenant_setting_id')
            ->when(!empty($ids), function ($query) use ($ids) {
                return $query->whereIn('ts.setting_id', $ids);
            })
            ->get();
    }

    /**
     * Create new activated settings
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data): bool
    {
        foreach ($data['settings'] as $value) {
            $this->tenantActivatedSetting->storeSettings($value['tenant_setting_id'], $value['value']);
        }
        return true;
    }

    /**
     * Fetch all tenant settings
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllTenantSettings(): Collection
    {
        return $this->tenantActivatedSetting->whereHas('settings')->get();
    }

    /**
     * Get fetch all activated tenant settings
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAllTenantActivatedSetting(Request $request): array
    {
        // Fetch tenant all settings details - From super admin
        $getTenantSettings = $this->helpers->getAllTenantSetting($request);

        // Get data from tenant database
        $tenantActivatedSettings = $this->tenantActivatedSetting->whereHas('settings')->get();

        $tenantSettingData = array();
        if ($tenantActivatedSettings->count() &&  $getTenantSettings->count()) {
            foreach ($tenantActivatedSettings as $settingKey => $tenantSetting) {
                $index = $getTenantSettings->search(function ($value, $key) use ($tenantSetting) {
                    return $value->tenant_setting_id === $tenantSetting->settings->setting_id;
                });
                $tenantSettingData[] = $getTenantSettings[$index]->key;
            }
        }
        return $tenantSettingData;
    }

    /**
     * Get fetch all activated tenant settings
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function checkTenantSettingStatus(string $settingKeyName, Request $request): bool
    {
        // Fetch tenant all settings details - From super admin
        $getTenantSettings =  $this->helpers->getAllTenantSetting($request);

        // Get data from tenant database
        $tenantActivatedSettings = $this->tenantActivatedSetting->whereHas('settings')->get();

        $tenantSettingData = array();
        if ($tenantActivatedSettings->count() &&  $getTenantSettings->count()) {
            foreach ($tenantActivatedSettings as $settingKey => $tenantSetting) {
                $index = $getTenantSettings->search(function ($value, $key) use ($tenantSetting) {
                    return $value->tenant_setting_id === $tenantSetting->settings->setting_id;
                });
                $tenantSettingData[] = $getTenantSettings[$index]->key;
            }
        }

        return in_array($settingKeyName, $tenantSettingData);
    }
}
