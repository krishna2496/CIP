<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;

class Organization extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'organization_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_id',
        'name',
        'legal_number',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city_id','state_id',
        'country_id',
        'postal_code'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'organization_id',
        'name',
        'legal_number',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
    ];
}
