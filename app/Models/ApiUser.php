<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Tenant;

class ApiUser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_user';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'api_user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tenant_id','api_key','api_secret','status'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
    * Defined has one relation for the tenant table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'tenant_id', 'tenant_id');
    }
}
