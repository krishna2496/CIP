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
}
