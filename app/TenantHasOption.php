<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantHasOption extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_has_option';

    /**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'tenant_option_id';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tenant_id','option_name','option_value'];

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
	protected $visible = ['tenant_option_id', 'option_name', 'option_value'];

    /**
     * The rules that are used to validate.
     *
     * @var array
     */
    public static $rules = [
        // Validation rules
    ];
    
}
