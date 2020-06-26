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
     * @param array $data
     * @param int $tenantId
     * @return bool
     */
    public function store(array $data, int $tenantId): bool;

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
     * @param array $request
     * @param int $tenatnId
     * @return bool
     */
    public function isDonationSettingEnabled(array $request, int $tenantId): bool;

    /**
    * Disable donation realted settings
    *
    * @param int $tenantId
    */
    public function disableDonationRelatedSettings(int $tenantId);

    /**
     * Check donation setting for related settings
     *
     * @param int $tenantId
     * @param string $key
     * @return bool
     */
    public function isTenantHasSetting(int $tenantId, string $key): bool;

    /**
     * Get id of donation related settings
     *
     * @return array
     */
    public function getDonationRelatedSettingIds(): array;
}
