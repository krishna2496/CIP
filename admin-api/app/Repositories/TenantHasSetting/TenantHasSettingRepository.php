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
     * Get setting id of donation_comment and donation_rating setting
     *
     * @return array
     */
    public function getCommentAndRatingSettingId(): array
    {
        $getSettingIdForMissionRatingAndComment = $this->tenantSetting->select('tenant_setting_id')
            ->where(['key' => 'donation_mission_comments'])
            ->orWhere(['key' => 'donation_mission_ratings'])
            ->get();
        $missionRatingAndCommentIds = array_values(array_column($getSettingIdForMissionRatingAndComment->toArray(), 'tenant_setting_id'));
        return $missionRatingAndCommentIds;
    }

    /**
     * Check donation setting enable/disables
     *
     * @param int $tenantId
     * @return app\Models\TenantHasSetting
     */
    public function isDonationSettingEnabled($tenantId): TenantHasSetting
    {
        $donationTenantSettings = $this->tenantSetting->where(['key' => 'donation'])->get();
        $donationSettingId = $donationTenantSettings[0]['tenant_setting_id'];
        $donationHasSetting = $this->tenantHasSetting->where(['tenant_id' => $tenantId, 'tenant_setting_id' => $donationSettingId])->firstOrFail();
        return $donationHasSetting;
    }
}
