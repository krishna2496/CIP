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
    public $tenantHasSetting;

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
            DB::raw("CASE WHEN tenant_has_setting.tenant_setting_id  IS NULL THEN 'Not Selected' ELSE 'Selected' END AS status ")
        )
        ->leftJoin('tenant_has_setting', function ($join) use ($tenantId) {
            $join->on('tenant_setting.tenant_setting_id', '=', 'tenant_has_setting.tenant_setting_id')
            ->where('tenant_has_setting.tenant_id', $tenantId);
        })
        ->get();
        return $tenantSettings;
    }
    
    /**
     * Create new setting
     *
     * @param \Illuminate\Http\Request $request
     * @param int $tenantId
     * @return bool
     */
    public function store(Request $request, int $tenantId): bool
    {
        foreach ($request->settings as $value) {
            $this->tenantHasSetting->storeSettings($tenantId, $value['tenant_setting_id']);
        }
        return true;
    }
}
