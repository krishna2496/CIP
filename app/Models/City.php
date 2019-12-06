<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'city_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['city_id', 'name', 'country_id', 'city_name'];

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['city_id', 'country_id'];
}
