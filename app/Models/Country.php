<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CountryTranslation;

class Country extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'country';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'country_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['country_id', 'ISO', 'translations'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_id', 'ISO'];

    public function translations()
    {
        return $this->hasMany(CountryTranslation::class, 'country_id', 'country_id');
    }
}
