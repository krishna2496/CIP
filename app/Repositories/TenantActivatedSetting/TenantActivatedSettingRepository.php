<?php
namespace App\Repositories\TenantActivatedSetting;

use App\Repositories\TenantActivatedSetting\TenantActivatedSettingInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\TenantActivatedSetting;

class TenantActivatedSettingRepository implements TenantActivatedSettingInterface
{
    /**
     * The tenantActivatedSetting for the model.
     *
     * @var App\Models\TenantActivatedSetting
     */
    public $tenantActivatedSetting;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\TenantActivatedSetting $tenantActivatedSetting
     * @return void
     */
    public function __construct(TenantActivatedSetting $tenantActivatedSetting)
    {
        $this->tenantActivatedSetting = $tenantActivatedSetting;
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
}
