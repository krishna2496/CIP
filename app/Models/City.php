<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CityTranslation;

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
    protected $visible = ['city_id', 'country_id', 'translations'];

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['city_id', 'country_id'];

    /**
     * Get the city translation associated with the city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CityTranslation::class, 'city_id', 'city_id');
    }
}
