<?php
namespace App\Repositories\Timesheet;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * get added action data count
     *
     * @param int $missionId
     * @return int
     */
    public function getAddedActions(int $missionId): int;
  
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
     * Update timesheet status on submit
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return bool
     */
    public function submitTimesheet(Request $request, int $userId): bool;

    /**
     * Fetch goal requests list
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function goalRequestList(Request $request): LengthAwarePaginator;

    /**
     * Fetch timesheet details by missionId and date
     *
     * @param int $missionId
     * @param string $date
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetailByDate(int $missionId, string $date): ? Collection;

    /**
     * Fetch timesheet details
     *
     * @param int $missionId
     * @param int $userId
     * @param string $date
     * @return null|Illuminate\Support\Collection
     */
    public function getTimesheetDetails(int $missionId, int $userId, string $date): ?Collection;

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
