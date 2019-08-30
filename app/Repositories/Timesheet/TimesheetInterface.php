<?php
namespace App\Repositories\Timesheet;

use Illuminate\Http\Request;
use App\Models\Timesheet;

interface TimesheetInterface
{
    /**
     * Store timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Timesheet
     */
    public function storeTimesheet(Request $request): Timesheet;

    /**
     * get added action data count
     *
     * @param int $missionId
     * @return int
     */
    public function getAddedActions(int $missionId): int;
    
    /**
     * Update timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @param int $timesheetId
     * @return bool
     */
    public function updateTimesheet(Request $request, int $timesheetId):  bool;

    /**
     * Fetch timesheet details
     *
     * @param int $timesheetId
     * @return null|Timesheet
     */
    public function find(int $timesheetId): ?Timesheet;

    /**
     * Fetch timesheet details
     *
     * @param int $timesheetId
     * @param int $userId
     * @return Timesheet
     */
    public function getTimesheetData(int $timesheetId, int $userId): Timesheet;

    /**
    * Remove the timesheet document.
    *
    * @param  int  $id
    * @return bool
    */
    public function delete(int $id): bool;
}
