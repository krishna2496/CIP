<?php
namespace App\Models;

use App\Models\TenantHasOption;
use App\Models\ApiUser;
use App\Models\TenantLanguage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tenant_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','sponsor_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['tenant_id', 'name', 'sponsor_id', 'status',
    'options', 'tenantLanguages', 'tenantLanguages.language'];

     
    /**
     * Variable which contains softDelete true.
     *
     * @var bool
     */
    protected $softDelete = true;
    
    /**
    * Defined has many relation for the tenant_option table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function options(): HasMany
    {
        return $this->hasMany(TenantHasOption::class, 'tenant_id', 'tenant_id');
    }

    /**
    * Defined has many relation for the languages table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function tenantLanguages(): HasMany
    {
        return $this->hasMany(TenantLanguage::class, 'tenant_id', 'tenant_id');
    }

    /**
    * Defined has many relation for the api_users table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function apiUsers(): HasMany
    {
        return $this->hasMany(ApiUser::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $id
     * @return self
     */
    public function findTenant(int $id): self
    {
        return static::with('options', 'tenantLanguages', 'tenantLanguages.language')->findOrFail($id);
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteTenant(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }
}
