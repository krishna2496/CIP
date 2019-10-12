<?php
namespace App\Models;

use App\Models\Mission;
use App\Models\TimesheetDocument;
use App\Models\TimesheetStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'notes', 'status', 'status_id'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['timesheet_id', 'user_id', 'mission_id', 'time', 'action', 'date_volunteered',
        'day_volunteered', 'notes', 'timesheetDocument', 'timesheetStatus', 'mission', 'month', 'total_hours',
        'total_minutes'];
    
    /**
     * Get date volunteered attribute on the model.
     *
     * @return null|string
     */
    public function getDateVolunteeredAttribute(): ?string
    {
        return ($this->attributes['date_volunteered'] != null) ?
        (new Carbon($this->attributes['date_volunteered']))->format('m-d-Y') : null;
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

    /**
     * Get time attribute on the model.
     *
     * @return null|string
     */
    public function getTimeAttribute(): ?string
    {
        return ($this->attributes['time'] != null) ? date('H:i', strtotime($this->attributes['time'])) : null;
    }
    
    /**
     * Set note attribute on the model.
     *
     * @param string $value
     * @return void
     */
    public function setNotesAttribute(string $value)
    {
        $this->attributes['notes'] = trim($value);
    }
}
