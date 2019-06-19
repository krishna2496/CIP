<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\{MissionDocument, MissionMedia, MissionLanguage, MissionApplication};

class Mission extends Model
{
    use SoftDeletes;

    protected $table = 'mission';
    protected $primaryKey = 'mission_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

	protected $fillable = ['theme_id', 'city_id', 'country_id', 'start_date', 'end_date', 'total_seats', 'available_seats', 'application_deadline', 'publication_status', 'organisation_id', 'organisation_name', 'mission_type', 'goal_objective'];

	/**
     * Get the document record associated with the mission.
     */
	public function missionDocument()
    {
    	return $this->hasMany(MissionDocument::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the media record associated with the mission.
     */
	public function missionMedia()
    {
    	return $this->hasMany(MissionMedia::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the language title record associated with the mission.
     */
	public function missionLanguage()
    {
    	return $this->hasMany(MissionLanguage::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the mission theme associated with the mission.
     */
    public function missionTheme()
    {
        return $this->belongsTo(MissionTheme::class, 'theme_id', 'mission_theme_id');
    }

    /**
     * Get city associated with the mission.
     */
    public function city()
    {
        return $this->hasOne(City::class, 'city_id', 'city_id')
         ->select('city_id', 'name');
    }

    /**
     * Get country associated with the mission.
     */
    public function country()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id')
         ->select('country_id', 'name');
    }

    /**
     * Get the mission application associated with the mission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionApplication()
    {
        return $this->hasMany(MissionApplication::class, 'mission_id', 'mission_id');

    }
}
