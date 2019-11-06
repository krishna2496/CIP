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
use App\Models\Comment;
use App\Models\Availability;
use App\Models\Timesheet;
use Illuminate\Notifications\Notifiable;

class Mission extends Model
{
    use SoftDeletes, Notifiable;

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
    'publication_status', 'organisation_id', 'organisation_name', 'mission_type',
    'organisation_detail', 'availability_id'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_id', 'theme_id', 'city_id',
    'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats',
    'publication_status', 'organisation_id', 'organisation_name', 'organisation_detail', 'mission_type',
    'missionDocument', 'missionMedia', 'missionLanguage', 'missionTheme', 'city',
    'default_media_type','default_media_path', 'default_media_name', 'title','short_description',
    'description','objective','set_view_detail','city_name',
    'seats_left','user_application_count','mission_application_count','missionSkill','city_name','missionApplication',
    'country','favouriteMission','missionInvite','missionRating', 'goalMission', 'timeMission', 'application_deadline',
    'application_start_date', 'application_end_date', 'application_start_time', 'application_end_time',
    'goal_objective', 'achieved_goal', 'mission_count', 'mission_rating_count',
    'already_volunteered','total_available_seat', 'available_seat','deadline',
    'favourite_mission_count', 'mission_rating', 'is_favourite', 'skill_id',
    'user_application_status', 'skill', 'rating', 'mission_rating_total_volunteers',
    'availability_id', 'availability_type', 'average_rating', 'timesheet', 'timesheetStatus', 'total_hours', 'time',
    'hours', 'action', 'ISO', 'total_minutes', 'custom_information'];
    
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
         ->select('country_id', 'name', 'ISO');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionSkill(): HasMany
    {
        return $this->hasMany(MissionSkill::class, 'mission_id', 'mission_id');
    }

    /**
     * Defined for goal mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function goalMission(): HasOne
    {
        return $this->hasOne(GoalMission::class, 'mission_id', 'mission_id');
    }

    /**
     * Defined for time mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function timeMission(): HasOne
    {
        return $this->hasOne(TimeMission::class, 'mission_id', 'mission_id');
    }
    
    /**
     * Get comment associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class, 'mission_id', 'mission_id');
    }

    /**
     * Get availability associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class, 'availability_id', 'availability_id');
    }

    /**
     * Get timesheet associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timesheet(): HasMany
    {
        return $this->hasMany(Timesheet::class, 'mission_id', 'mission_id');
    }

    /**
     * Soft delete from the database.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteMission(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }

    /**
     * Get an attribute from the model.
     *
     * @return string
     */
    public function getCityNameAttribute(): string
    {
        return $this->city()->select('name')->first()->name;
    }

    /**
     * Set start date attribute on the model.
     *
     * @param $value
     * @return void
     */
    public function setStartDateAttribute($value): void
    {
        $this->attributes['start_date'] = (($value !== null) && strlen(trim($value)) > 0) ?
        Carbon::parse($value, config('constants.TIMEZONE'))->setTimezone(config('app.TIMEZONE')) : null;
    }

    /**
     * Get start date attribute from the model.
     *
     * @return string
     */
    public function getStartDateAttribute(): ?string
    {
        return (isset($this->attributes['start_date']) && !empty(config('constants.TIMEZONE'))) ?
        Carbon::parse($this->attributes['start_date'])->setTimezone(config('constants.TIMEZONE'))
            ->format(config('constants.DB_DATE_TIME_FORMAT')):
            null;
    }
    
    /**
     * Set end date attribute on the model.
     *
     * @param $value
     * @return void
     */
    public function setEndDateAttribute($value): void
    {
        $this->attributes['end_date'] = ($value !== null && strlen(trim($value)) > 0) ?
        Carbon::parse($value, config('constants.TIMEZONE'))->setTimezone(config('app.TIMEZONE')) : null;
    }
    
    /**
     * Get end date attribute from the model.
     *
     * @return null|string
     */
    public function getEndDateAttribute(): ?string
    {
        return (isset($this->attributes['end_date']) && !empty(config('constants.TIMEZONE'))) ?
             Carbon::parse($this->attributes['end_date'])->setTimezone(config('constants.TIMEZONE'))
             ->format(config('constants.DB_DATE_TIME_FORMAT')):
             null;
    }
    
    /**
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
            $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
            config("constants.application_status")["PENDING"]]);
        }])->first();
    }

    /**
     * Set organisation detail in serialize form
     *
     * @param array|null $value
     * @return void
     */
    public function setOrganisationDetailAttribute($value)
    {
        if (!is_null($value) && !empty($value)) {
            $this->attributes['organisation_detail'] = serialize($value);
        }
    }

    /**
     * Get organisation detail in unserialize form
     *
     * @param string|null $value
     * @return null|array
     */
    public function getOrganisationDetailAttribute($value)
    {
        if (!is_null($value) && ($value !== '')) {
            $data = @unserialize($value);
            if ($data !== false) {
                return (!is_null($value) && ($value !== '')) ? unserialize($value) : null;
            }
        }
        return null;
    }

    /**
     * Get users associated with the mission availability.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availableUsers(): HasMany
    {
        return $this->hasMany('App\User', 'availability_id', 'availability_id');
    }
}
