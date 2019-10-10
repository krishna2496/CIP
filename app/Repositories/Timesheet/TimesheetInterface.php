<?php
namespace App\Repositories\Timesheet;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use Illuminate\Support\Collection;

interface TimesheetInterface
{
    /**
     * Store/Update timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Timesheet
     */
    public function storeOrUpdateTimesheet(Request $request): Timesheet;

    /**
     * get submitted action count
     *
     * @param int $missionId
     * @return int
     */
    public function getSubmittedActions(int $missionId): int;
  
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
    * @param  int  $timesheetId
    * @return bool
    */
    public function delete(int $id, int $timesheetId): bool;

    /**
     * Update timesheet status
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return bool
     */
    public function submitTimesheet(Request $request, int $userId): bool;

    /**
     * Get time request details.
     *
     *
     * @param \Illuminate\Http\Request $request
     * @param array $statusArray
     * @return Object
     */
    public function timeRequestList(Request $request, array $statusArray) : Object;

    /**
     * Fetch goal requests list
     *
     * @param Illuminate\Http\Request $request
     * @param array $statusArray
     * @return Object
     */
    public function goalRequestList(Request $request, array $statusArray): Object;

    /**
     * Fetch timesheet details
     *
     * @param int $missionId
     * @param int $userId
     * @param string $date
     * @param array $timesheetStatus
     *
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetails(int $missionId, int $userId, string $date, array $timesheetStatus): ?Collection;

    /**
     * Update timesheet field value, based on timesheet_id condition
     *
     * @param int $statusId
     * @param int $timesheetId
     * @return bool
     */
    public function updateTimesheetStatus(int $statusId, int $timesheetId): bool;
    
    /**
     * Get timesheet entries
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getAllTimesheetEntries(Request $request): array;
}
