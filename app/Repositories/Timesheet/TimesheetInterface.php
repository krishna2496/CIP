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
}
