<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TimeMission extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_mission';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'time_mission_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'application_deadline', 'application_start_date', 'application_end_date',
     'application_start_time','application_end_time'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_id', 'application_deadline', 'application_start_date',
    'application_end_date', 'application_start_time', 'application_end_time'];

    /**
     * Set application deadline date attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationDeadlineAttribute($value)
    {
        $this->attributes['application_deadline'] = ($value != null) ?
        Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT')) : null;
    }

    /**
     * Set application start date date attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationStartDateAttribute($value)
    {
        $this->attributes['application_start_date'] = ($value != null) ?
        Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT')) : null;
    }

    /**
     * Set application end date date attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationEndDateAttribute($value)
    {
        $this->attributes['application_end_date'] = ($value != null) ?
        Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT')) : null;
    }

    /**
     * Set application start time attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationStartTimeAttribute($value)
    {
        $this->attributes['application_start_time'] = ($value != null) ?
        Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT')) : null;
    }

    /**
     * Set application end time attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setApplicationEndTimeAttribute($value)
    {
        $this->attributes['application_end_time'] = ($value != null) ?
        Carbon::parse($value)->format(config('constants.DB_DATE_FORMAT')) : null;
    }

    /*
    * Get deadline for mission.
    *
    * @param int $missionId
    * @return string
    */
    public function getDeadLine(int $missionId): string
    {
        return $this->where('mission_id', $missionId)->value('application_deadline');
    }
}
