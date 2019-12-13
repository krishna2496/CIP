<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CountryLanguage;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $visible = ['country_id', 'ISO', 'translations', 'languages'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_id', 'ISO'];

    /**
     * Get languages associated with the country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languages()
    {
        return $this->hasMany(Countrylanguage::class, 'country_id', 'country_id');
    }

    /**
     * Soft delete the model from the database.
     *
     * @param  int $id
     * @return bool
     */
    public function deleteCountry(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }

    /**
     * Set ISO attribute on the model.
     *
     * @param $value
     * @return void
     */
    public function setISOAttribute($value)
    {
        $this->attributes['ISO'] = strtoupper($value);
    }
}
