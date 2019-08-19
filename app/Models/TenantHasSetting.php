<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TenantSetting;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Collection;

class TenantHasSetting extends Model
{
    use SoftDeletes;

    /**
    * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_has_setting';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tenant_setting_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tenant_setting_id', 'tenant_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['tenant_setting_id', 'tenant_id', 'setting', 'getsettings'];

    /**
     * The rules that should validate request.
     *
     * @var array
     */
    public static $rules = [
        // Validation rules
    ];
    
    /**
    * Defined has many relation for the tenant_setting table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function setting(): HasOne
    {
        return $this->hasOne(TenantSetting::class, 'tenant_setting_id', 'tenant_setting_id');
    }
   
    /**
    * Defined has many relation for the tenant_setting table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function settings()
    {
        return $this->hasOne(TenantSetting::class, 'tenant_setting_id', 'tenant_setting_id');
    }

    /**
    * Defined has many relation for the tenant_setting table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function getSettings()
    {
        return $this->belongsTo('App\Models\TenantSetting', 'tenant_setting_id', 'tenant_setting_id');
    }

    /**
     * Store/update settings.
     *
     * @param  int  $tenantId
     * @param  int  $tenantSettingId
     * @param  int  $value
     * @return bool
     */
    public function storeSettings(int $tenantId, int $tenantSettingId, int $value): bool
    {
        if ($value == 1) {
            return static::firstOrNew(array('tenant_id' => $tenantId, 'tenant_setting_id' => $tenantSettingId))->save();
        } else {
            return static::where(['tenant_id' => $tenantId, 'tenant_setting_id' => $tenantSettingId])->delete();
        }
    }
}
