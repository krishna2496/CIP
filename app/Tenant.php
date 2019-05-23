<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TenantHasOption;

class Tenant extends Model {

	protected $table = 'tenant';

	protected $primaryKey = 'tenant_id';
	
    protected $fillable = ['name','sponsor_id'];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public static $rules = [
        // Validation rules
    ];

    /*
    * Defined has many relation for the tenant_option table.
    */
    public function options()
    {
    	return $this->hasMany(TenantHasOption::class,'tenant_id','tenant_id');
    }
}
