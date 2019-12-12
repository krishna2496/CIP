<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryTranslation extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'country_translation';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'country_translation_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['country_translation_id', 'country_id', 'language_id', 'name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_translation_id', 'country_id', 'language_id', 'name'];
}
