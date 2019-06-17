<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\{TenantHasOption, ApiUser, TenantLanguage};

class Tenant extends Model {

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
    protected $visible = ['tenant_id', 'name', 'sponsor_id', 'status', 'options', 'tenantLanguages', 'tenantLanguages.language'];

     /**
     * The rules that should validate request.
     *
     * @var array
     */
     public $rules = [
        // Validation rules
        'name' => 'required|unique:tenant,name,NULL,tenant_id,deleted_at,NULL',
        'sponsor_id'  => 'required',
    ];

    /**
     * Variable which contains softDelete true.
     *
     * @var bool
     */
     protected $softDelete = true;
    
    /*
    * Defined has many relation for the tenant_option table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function options()
    {
    	return $this->hasMany(TenantHasOption::class, 'tenant_id', 'tenant_id');
    }

    /*
    * Defined has many relation for the languages table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function tenantLanguages()
    {
        return $this->hasMany(TenantLanguage::class, 'tenant_id', 'tenant_id');
    }

    /*
    * Defined has many relation for the api_users table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function apiUsers()
    {
        return $this->hasMany(ApiUser::class, 'tenant_id', 'tenant_id');
    }
	
    /**
     * Get the language record associated with the tenant language.
     */
	public function getAll()
    {
        return static::with('options','tenantLanguages','tenantLanguages.language')->paginate(config('constants.PER_PAGE_LIMIT'));
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function findTenant(int $id)
    {
        return static::with('options','tenantLanguages','tenantLanguages.language')->findOrFail($id);
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function deleteTenant(int $id)
    {
        return static::findOrFail($id)->delete();
    }
}
