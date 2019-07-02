<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MissionDocument;
use App\Models\MissionMedia;
use App\Models\MissionLanguage;
use App\Models\MissionApplication;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Mission extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['theme_id', 'city_id',
    'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats', 'application_deadline',
    'publication_status', 'organisation_id', 'organisation_name', 'mission_type', 'goal_objective'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_id', 'theme_id', 'city_id',
    'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats', 'application_deadline',
    'publication_status', 'organisation_id', 'organisation_name', 'mission_type', 'goal_objective',
    'missionDocument', 'missionMedia', 'missionLanguage', 'missionTheme', 'city'];

    protected $appends = ['city_name'];
    /**
     * Get the document record associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionDocument(): HasMany
    {
        return $this->hasMany(MissionDocument::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the media record associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionMedia(): HasMany
    {
        return $this->hasMany(MissionMedia::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the language title record associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function missionLanguage(): HasMany
    {
        return $this->hasMany(MissionLanguage::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the mission theme associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function missionTheme(): BelongsTo
    {
        return $this->belongsTo(MissionTheme::class, 'theme_id', 'mission_theme_id');
    }

    /**
     * Get city associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'city_id', 'city_id')
         ->select('city_id', 'name');
    }

    /**
     * Get country associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id')
         ->select('country_id', 'name');
    }

    /**
     * Get the mission application associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionApplication(): HasMany
    {
        return $this->hasMany(MissionApplication::class, 'mission_id', 'mission_id');
    }

    /**
     * Soft delete from the database.
     *
     * @param  int  $id
     * @return void
     */
    public function deleteMission(int $id)
    {
        $mission = static::findOrFail($id)->delete();
        // static::missionMedia()->delete();
        // static::missionLanguage()->delete();
        // static::missionDocument()->delete();
        return $mission;
    }

    /**
     * Get an attribute from the model.
     *
     * @return string
     */
    public function getCityNameAttribute()
    {
        return $this->city()->select('name')->first()->name;
    }

    /**
     * Set start date attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setStartDateAttribute($value)
    {
        if ($value != null) {
            $this->attributes['start_date'] = Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT'));
        } else {
            $this->attributes['start_date'] = null;
        }
    }

    /**
     * Set end date attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setEndDateAttribute($value)
    {
        if ($value != null) {
            $this->attributes['end_date'] = Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT'));
        } else {
            $this->attributes['end_date'] = null;
        }
    }

    /**
     * Set application deadline attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationDeadlineAttribute($value)
    {
        if ($value != null) {
            $this->attributes['application_deadline'] = Carbon::parse($value)
            ->format(config('constants.DB_DATE_FORMAT'));
        } else {
            $this->attributes['application_deadline'] = null;
        }
    }
}
