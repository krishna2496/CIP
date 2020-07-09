<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantCurrency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_currency';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tenant_id';

    /**
     * Auto increment is disable.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'tenant_id',  'default', 'is_active'];

    /**
     * The attributes that are visible.
     *
     * @var array
     */
    protected $visible = ['code', 'default', 'is_active'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
