<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantHasOption extends Model
{
    protected $table = 'tenant_has_option';

	protected $primaryKey = 'tenant_option_id';
	
    protected $fillable = ['tenant_id','option_name','option_value'];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public static $rules = [
        // Validation rules
    ];
}
