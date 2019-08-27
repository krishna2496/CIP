<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
