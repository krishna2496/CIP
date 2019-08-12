<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class TenantOption extends Model
{
    use SoftDeletes;

    /**
    * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_option';

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
    protected $fillable = ['option_title', 'option_description', 'option_key'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['tenant_option_id', 'option_title', 'option_description', 'option_key'];

    /**
     * The rules that should validate request.
     *
     * @var array
     */
    public static $rules = [
        // Validation rules
    ];
}
