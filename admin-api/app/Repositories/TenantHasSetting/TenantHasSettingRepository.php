<?php
namespace App\Repositories\TenantHasSetting;

use App\Repositories\TenantHasSetting\TenantHasSettingInterface;
use Illuminate\Http\Request;
use App\Models\TenantHasSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\TenantSetting;
use DB;

class TenantHasSettingRepository implements TenantHasSettingInterface
{
    /**
     * @var App\Models\TenantHasSetting
     */
    private $tenantHasSetting;

    /**
     * Create a new Tenant has setting repository instance.
     *
     * @param  App\Models\TenantHasSetting $tenantHasSetting
     * @param  App\Models\TenantSetting $tenantSetting
     * @return void
     */
    public function __construct(TenantHasSetting $tenantHasSetting, TenantSetting $tenantSetting)
    {
        $this->tenantHasSetting = $tenantHasSetting;
        $this->tenantSetting = $tenantSetting;
    }

    /**
     * Get Settings lists
     *
     * @param int $tenantId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSettingsList(int $tenantId): Collection
    {
        $tenantSettings = $this->tenantSetting
        ->select(
            'tenant_setting.title',
            'tenant_setting.tenant_setting_id',
            'tenant_setting.description',
            'tenant_setting.key',
            DB::raw("CASE WHEN tenant_has_setting.tenant_setting_id  IS NULL THEN '0' ELSE '1' END AS is_active ")
        )
        ->leftJoin('tenant_has_setting', function ($join) use ($tenantId) {
            $join->on('tenant_setting.tenant_setting_id', '=', 'tenant_has_setting.tenant_setting_id')
            ->whereNull('tenant_has_setting.deleted_at')
            ->where('tenant_has_setting.tenant_id', $tenantId);
        })
        ->get();
        return $tenantSettings;
    }
    
    /**
     * Create new setting
     *
     * @param int $tenantId
     * @param int $tenantSettingId
     * @param int $value
     * @return bool
     */
    public function store(int $tenantId, int $tenantSettingId, int $value): bool
    {
        if ($value === 1) {
            $this->tenantHasSetting->enableSetting($tenantId, $tenantSettingId);
        } else {
            $this->tenantHasSetting->disableSetting($tenantId, $tenantSettingId);
        }

        return true;
    }

    /**
     * Get key by setting Id
     *
     * @param int $tenantSettingId
     * @return string
     */
    public function getKeyBySettingID(int $tenantSettingId): string
    {
        $tenantSettingKey = $this->tenantSetting->select('key')->where(['tenant_setting_id' => $tenantSettingId])->get();
        return $tenantSettingKey->toArray()[0]['key'];
    }

    /**
     * Check donation setting enable/disables and update other setting
     *
     * @param Request $request
     * @param int $tenantId
     * @return bool
     */
    public function isDonationSettingEnabled(Request $request, int $tenantId): bool
    {
        $settingData = $request->toArray();
        foreach ($settingData['settings'] as $value) {
            $tenantSettingId = $value['tenant_setting_id'];
            $settingValue = $value['value'];
            $key = $this->getKeyBySettingID($tenantSettingId);

            // Donation setting is disable then donation_commnet and donation_rating will be disabled
            if ($key === 'donation' && $value['value'] === '0') {
                $this->disableDonationRelatedSetting($tenantId);
            }

            // check donation setting enable/disable for donation_commnet and donation_rating
            if ($key === 'donation_mission_comments' || $key === 'donation_mission_ratings') {
                if (!$this->checkDonationSettingForRelatedSettings($tenantId)) {
                    return false;
                }
            }
            $this->store($tenantId, $tenantSettingId, $settingValue);
        }

        return true;
    }
    
    /**
     * disable all doantion related setting when donation setting is disabled
     *
     * @param int $tenantId
     * @return bool
     */
    public function disableDonationRelatedSetting(int $tenantId)
    {
        $relatedSettingArray = ['donation_mission_comments', 'donation_mission_ratings'];
        foreach ($relatedSettingArray as $value) {
            $settingIdDetails = $this->tenantSetting->select('tenant_setting_id')
            ->where(['key' => $value])
            ->get();
            $settingId = $settingIdDetails[0]['tenant_setting_id'];
            $this->store($tenantId, $settingId, 0);
        }
    }

    /**
     * Check donation setting enable/disables
     *
     * @param int $tenantId
     * @return bool
     */
    public function checkDonationSettingForRelatedSettings(int $tenantId): bool
    {
        $donationTenantSettings = $this->tenantSetting->where(['key' => 'donation'])->get();
        $donationSettingId = $donationTenantSettings[0]['tenant_setting_id'];
        $donationHasSetting = $this->tenantHasSetting->where(['tenant_id' => $tenantId, 'tenant_setting_id' => $donationSettingId])->get();
        if (!empty($donationHasSetting->toArray())) {
            return true;
        }
        return false;
    }
}
