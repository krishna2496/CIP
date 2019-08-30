<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use App\Models\Mission;

class Timesheet extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timesheet';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'timesheet_id';
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['timesheet_id', 'user_id', 'mission_id', 'time', 'action', 'date_volunteered',
    'day_volunteered',
    'notes', 'status'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['user_id', 'mission_id', 'time', 'action', 'date_volunteered', 'day_volunteered',
    'notes', 'status'];

    /**
     * Get the mission associated with timesheet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mission(): HasOne
    {
        return $this->hasOne(Mission::class, 'mission_id', 'mission_id');
    }

    /**
     * Get the user associated with timesheet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    /**
     * Set application start date attribute on the model.
     *
     * @param  mixed $value
     * @return void
     */
    public function setDateVolunteeredAttribute($value)
    {
        $this->attributes['date_volunteered'] = ($value != null) ?
        Carbon::createFromFormat('m-d-Y', $value)->setTimezone(config('constants.TIMEZONE')) : null;
    }
}
