<?php
namespace App\Repositories\TenantHasSetting;

use Illuminate\Http\Request;
use App\Models\TenantHasSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TenantHasSettingInterface
{
    /**
     * Get Settings lists
     *
     * @param int $tenantId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSettingsList(int $tenantId): Collection;

    /**
     * Create new setting
     *
     * @param int $tenantId
     * @param int $tenantSettingId
     * @param int $value
     * @return bool
     */
    public function store(int $tenantId, int $tenantSettingId, int $value): bool;

    /**
     * Get setting key by setting Id
     *
     * @param int $tenantSettingId
     * @return string
     */
    public function getKeyBySettingID(int $tenantSettingId): string;

    /**
     * Check donation setting is enable or disable
     *
     * @param Request $request
     * @param int $tenatnId
     * @return bool
     */
    public function isDonationSettingEnabled(Request $request, int $tenantId): bool;

    /**
    * Disable donation realted settings
    *
    * @param int $tenantId
    */
    public function disableDonationRelatedSetting(int $tenantId);

    /**
     * Check donation setting for related settings
     *
     * @param int $tenantId
     * @return bool
     */
    public function checkDonationSettingForRelatedSettings(int $tenantId): bool;
}
