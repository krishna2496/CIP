<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city_translation';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'city_translation_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['city_translation_id', 'city_id', 'language_id', 'name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['city_translation_id', 'city_id', 'language_id', 'name'];
}
