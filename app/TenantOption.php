<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantOption extends Model
{
    protected $table = 'tenant_option';
    protected $primaryKey = 'tenant_option_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['option_name','option_value'];

}
