<?php
namespace App\Repositories\TenantSetting;

use App\Repositories\TenantSetting\TenantSettingInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Helpers\ResponseHelper;
use App\Models\TenantSetting;
use Illuminate\Http\Request;
use PDOException;
use Validator;
use DB;

class TenantSettingRepository implements TenantSettingInterface
{

    /**
     * The tenantSetting for the model.
     *
     * @var App\Models\TenantSetting
     */
    public $tenantSetting;
    
    public function __construct(TenantSetting $tenantSetting)
    {
        $this->tenantSetting = $tenantSetting;
    }
    
    /**
     * Update setting value
     *
     * @param array $data
     */
    public function updateSetting(array $data, int $settingId)
    {           
        $setting = $this->tenantSetting->findOrFail($settingId);
        $setting->update($data);
        return $setting;
    }

    /**
    * Get all tenant's settings data
    *
    * @param Illuminate\Http\Request $request
    * @return Illuminate\Pagination\LengthAwarePaginator
    */
    public function getAllSettings(): LengthAwarePaginator
    {
        return $this->tenantSetting->paginate(config('constants.PER_PAGE_LIMIT'));        
    }

}
