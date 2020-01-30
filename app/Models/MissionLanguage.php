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

    protected $fillable = [
        'mission_id',
        'language_id',
        'title',
        'description',
        'objective',
        'short_description',
        'custom_information'
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'lang',
        'language_id',
        'language_code',
        'title',
        'objective',
        'short_description',
        'description',
        'custom_information'
    ];

    /**
     * Set description attribute on the model.
     *
     * @param array $value
     * @return void
     */
    public function setDescriptionAttribute(array $value)
    {
        if (empty($value)) {
            $this->attributes['description'] = null;
        } else {
            $this->attributes['description'] = serialize($value);
        }
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param $value
     * @return null|array
     */
    public function getDescriptionAttribute($value)
    {
        if ($value) {
            return unserialize($value);
        }
    }

    /**
     * Store/update specified resource.
     *
     * @param  array $condition
     * @param  array $data
     * @return App\Models\MissionLanguage
     */
    public function createOrUpdateLanguage(array $condition, array $data): MissionLanguage
    {
        return static::updateOrCreate($condition, $data);
    }

    /**
     * Get specified resource.
     *
     * @param int $missionId
     * @param int $languageId
     * @return string
     */
    public function getMissionName(int $missionId, int $languageId): string
    {
        return static::select('title')
        ->where(['mission_id' => $missionId, 'language_id' => $languageId])->value('title');
    }

    /**
     * Set custom conformation attribute on the model.
     *
     * @param $value
     * @return void
     */
    public function setCustomInformationAttribute($value)
    {
        $this->attributes['custom_information'] = isset($value) ? serialize($value) : null;
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param $value
     * @return null|array
     */
    public function getCustomInformationAttribute($value)
    {
        if ($value) {
            return unserialize($value);
        }
    }
}
