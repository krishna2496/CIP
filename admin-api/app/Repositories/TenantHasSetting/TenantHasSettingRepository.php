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
     * @param array $data
     * @param int $tenantId
     * @return bool
     */
    public function store(array $data, int $tenantId): bool
    {
        $donationRelatedSettingIdsArray = $this->getDonationRelatedSettingIds();
        $donationDisableFlag = false;
        $donationRelatedSettingsExist = false;
        foreach ($data['settings'] as $value) {
            $key = $this->getKeyBySettingId($value['tenant_setting_id']);
            if ($value['value'] == 1) {
                if (in_array($value['tenant_setting_id'], $donationRelatedSettingIdsArray)) {
                    $donationRelatedSettingsExist = true;
                }
                $this->tenantHasSetting->enableSetting($tenantId, $value['tenant_setting_id']);
            } else {
                if ($key === 'donation' && $value['value'] === '0') {
                    $donationDisableFlag = true;
                    $this->disableDonationRelatedSettings($tenantId);
                }
                $this->tenantHasSetting->disableSetting($tenantId, $value['tenant_setting_id']);
            }
        }

        if ($donationDisableFlag === true && $donationRelatedSettingsExist === true) {
            $this->disableDonationRelatedSettings($tenantId);
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
     * @param array $request
     * @param int $tenantId
     * @return bool
     */
    public function isDonationSettingEnabled(array $settingData, int $tenantId): bool
    {
        $donationSettingEnable = false;
        $checkDonationRelatedSettings = [ 'donation', 'donation_mission_comments', 'donation_mission_ratings'];
        foreach ($settingData['settings'] as $value) {
            $tenantSettingId = $value['tenant_setting_id'];
            $settingValue = $value['value'];
            $key = $this->getKeyBySettingID($tenantSettingId);

            // check donation setting enable/disable for donation_comment and donation_rating
            if (in_array($key, $checkDonationRelatedSettings)) {
                if (!$this->isTenantHasSetting($tenantId, 'donation')) {
                    $donationSettingEnable = true;
                } else {
                    $donationSettingEnable = false;
                }
            }

            // Check donation setting is alos updating or not
            if ($key === 'donation' && $settingValue === '1' && $donationSettingEnable === true) {
                return true;
            } elseif ($key === 'donation' && $settingValue === '0' && $donationSettingEnable === false) {
                return true;
            }
        }

        if ($donationSettingEnable === true) {
            return false;
        }

        return true;
    }
    
    /**
     * Disable all donation related settings when donation setting is disabled
     *
     * @param int $tenantId
     */
    public function disableDonationRelatedSettings(int $tenantId)
    {
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');
        foreach ($donationRelatedSettingsArray as $value) {
            $settingIdDetails = $this->tenantSetting->select('tenant_setting_id')
            ->where(['key' => $value])
            ->get();
            $settingId = $settingIdDetails[0]['tenant_setting_id'];
            $this->tenantHasSetting->disableSetting($tenantId, $settingId);
        }
    }

    /**
     * Return data if tenant has donation setting is enable/disable
     *
     * @param int $tenantId
     * @param string $key
     * @return bool
     */
    public function isTenantHasSetting(int $tenantId, string $key): bool
    {
        $donationTenantSettings = $this->tenantSetting->where(['key' => $key])->get();
        $donationSettingId = $donationTenantSettings[0]['tenant_setting_id'];
        $donationHasSetting = $this->tenantHasSetting->where(['tenant_id' => $tenantId, 'tenant_setting_id' => $donationSettingId])->get();
        if (!empty($donationHasSetting->toArray())) {
            return true;
        }
        return false;
    }

    /**
     * Get donation related setting IDs
     *
     * @return array
     */
    public function getDonationRelatedSettingIds(): array
    {
        $donationRelatedSettingsArray = config('constants.DONATION_RELATED_SETTINGS');
        $settingIdArray = [];
        foreach ($donationRelatedSettingsArray as $value) {
            $settingIdDetails = $this->tenantSetting->select('tenant_setting_id')
            ->where(['key' => $value])
            ->get();
            $settingId = $settingIdDetails[0]['tenant_setting_id'];
            array_push($settingIdArray, $settingId);
        }
        return $settingIdArray;
    }
}
