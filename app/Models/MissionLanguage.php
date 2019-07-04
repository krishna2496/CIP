<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionLanguage extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_language';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['mission_id', 'language_id', 'title', 'description', 'objective', 'short_description'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_language_id', 'lang', 'language_id', 'title', 'objective', 'short_description',
                         'description'];

    /**
     * Get the mission that has language titles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }

    /**
     * Set description attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = serialize($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getDescriptionAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Store/update specified resource.
     *
     * @param  array $condition
     * @param  array $data
     * @return array
     */
    public function createOrUpdateLanguage(array $condition, array $data)
    {
        return static::updateOrCreate($condition, $data);
    }
}
