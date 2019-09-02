<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Mission;
use App\Models\TimesheetDocument;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\TimesheetStatus;

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
    protected $visible = ['timesheet_id', 'user_id', 'mission_id', 'time', 'action', 'date_volunteered',
    'day_volunteered', 'notes', 'timesheetDocument', 'timesheetStatus','mission'];

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

    /**
     * Get date volunteered attribute on the model.
     *
     * @return null|string
     */
    public function getDateVolunteeredAttribute(): ?string
    {
        return ($this->attributes['date_volunteered'] != null) ?
        (new Carbon($this->attributes['date_volunteered']))->format('m-d-Y'): null;
    }

    /**
     * Get the timesheet document record associated with the timesheet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timesheetDocument(): HasMany
    {
        return $this->hasMany(TimesheetDocument::class, 'timesheet_id', 'timesheet_id');
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function findTimesheet(int $timesheetId, int $userId)
    {
        return static::with('timesheetDocument', 'timesheetStatus')->where(['timesheet_id' => $timesheetId,
        'user_id' => $userId])->firstOrFail();
    }

    /**
     * Get the timesheet status record associated with the timesheet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timesheetStatus(): BelongsTo
    {
        return $this->belongsTo(TimesheetStatus::class, 'status_id', 'timesheet_status_id');
    }
}
