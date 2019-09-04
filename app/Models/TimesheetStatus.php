<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Timesheet;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class TimesheetStatus extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timesheet_status';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'timesheet_status_id';
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['timesheet_status_id', 'status'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['timesheet_status_id','status'];

    /**
     * Get the mission that has theme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timesheet(): HasMany
    {
        return $this->hasMany(Timesheet::class, 'timesheet_status_id', 'status_id');
    }

    /**
     * Get the success statuses' ids
     *
     * @return Illuminate\Support\Collection
     */
    public function getApprovedStatuses(): Collection
    {
        return $this->select('timesheet_status_id')
        ->whereIn('status', [
            config('constants.timesheet_status.AUTOMATICALLY_APPROVED'),
            config('constants.timesheet_status.APPROVED')
        ])->get();
    }
}
