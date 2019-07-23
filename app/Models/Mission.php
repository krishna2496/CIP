<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MissionDocument;
use App\Models\MissionMedia;
use App\Models\MissionLanguage;
use App\Models\MissionApplication;
use App\Models\Country;
use App\Models\FavouriteMission;
use App\Models\MissionInvite;
use App\Models\MissionRating;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\GoalMission;
use App\Models\TimeMission;

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

    /*
     * @var App\Helpers\Helpers
     */

    private $helpers;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['theme_id', 'city_id',
    'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats',
    'publication_status', 'organisation_id', 'organisation_name', 'mission_type'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_id', 'theme_id', 'city_id',
    'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats',
    'publication_status', 'organisation_id', 'organisation_name', 'mission_type',
    'missionDocument', 'missionMedia', 'missionLanguage', 'missionTheme', 'city',
    'default_media_type','default_media_path','title','short_description','objective','set_view_detail','city_name',
    'seats_left','user_application_count','mission_application_count','missionSkill','city_name','missionApplication',
    'country','favouriteMission','missionInvite','missionRating', 'goalMission', 'timeMission', 'application_deadline',
    'application_start_date', 'application_end_date', 'application_start_time', 'application_end_time',
    'goal_objective', 'mission_count', 'mission_rating_count','already_volunteered','total_available_seat',
    'available_seat','deadline','favourite_mission_count'];

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
     * Get favourite mission associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favouriteMission(): HasMany
    {
        return $this->hasMany(FavouriteMission::class, 'mission_id', 'mission_id');
    }

    /**
     * Get invite mission associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionInvite(): HasMany
    {
        return $this->hasMany(MissionInvite::class, 'mission_id', 'mission_id');
    }

    /**
     * Get rating associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionRating(): HasMany
    {
        return $this->hasMany(MissionRating::class, 'mission_id', 'mission_id');
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
     * Get the mission skill associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function missionSkill(): HasMany
    {
        return $this->hasMany(MissionSkill::class, 'mission_id', 'mission_id');
    }

    /**
     * Defined for goal mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function goalMission(): HasOne
    {
        return $this->hasOne(GoalMission::class, 'mission_id', 'mission_id');
    }

    /**
     * Defined for time mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function timeMission(): HasOne
    {
        return $this->hasOne(TimeMission::class, 'mission_id', 'mission_id');
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
        $this->attributes['start_date'] = ($value != null) ?
        Carbon::parse($value, config('constants.TIMEZONE'))->setTimezone(config('app.TIMEZONE')) : null;
    }

    /**
     * Get start date attribute from the model.
     *
     * @return string
     */
    public function getStartDateAttribute()
    {
        if (isset($this->attributes['start_date']) && !empty(config('constants.TIMEZONE'))) {
            return Carbon::parse($this->attributes['start_date'])->setTimezone(config('constants.TIMEZONE'));
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
        $this->attributes['end_date'] = ($value != null) ?
        Carbon::parse($value, config('constants.TIMEZONE'))->setTimezone(config('app.TIMEZONE')) : null;
    }
    
    /**
     * Get end date attribute from the model.
     *
     * @return string
     */
    public function getEndDateAttribute()
    {
        if (isset($this->attributes['end_date']) && !empty(config('constants.TIMEZONE'))) {
            return Carbon::parse($this->attributes['end_date'])->setTimezone(config('constants.TIMEZONE'));
        }
    }
    
    /*
    * Check seats are available or not.
    *
    * @param int $missionId
    * @return App\Models\Mission
    */
    public function checkAvailableSeats(int $missionId): Mission
    {
        return $this->select('*')
        ->where('mission.mission_id', $missionId)
        ->withCount(['missionApplication as mission_application_count' => function ($query) use ($missionId) {
            $query->where('approval_status', config("constants.application_status")["AUTOMATICALLY_APPROVED"]);
        }])->first();
    }
}
