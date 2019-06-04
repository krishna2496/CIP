<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantOption extends Model
{
	use SoftDeletes;
    protected $table = 'tenant_option';
    protected $primaryKey = 'tenant_option_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['option_name','option_value'];

}
