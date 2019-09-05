<?php
namespace App\Repositories\MissionSkill;

use App\Repositories\MissionSkill\MissionSkillInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MissionSkill;
use App\Models\TimesheetStatus;
use Illuminate\Support\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

class MissionSkillRepository implements MissionSkillInterface
{
    /**
     * @var App\Models\MissionSkill
     */
    public $missionSkill;

    /**
     * @var App\Models\TimesheetStatus
     */
    public $timesheetStatus;
 
    /**
     * Create a new MissionSkill repository instance.
     *
     * @param  App\Models\MissionSkill $missionSkill
     * @param App\Models\TimesheetStatus $timesheetStatus
     * @return void
     */
    public function __construct(MissionSkill $missionSkill, TimesheetStatus $timesheetStatus)
    {
        $this->missionSkill = $missionSkill;
        $this->timesheetStatus = $timesheetStatus;
    }

    /**
     * Get all skill history with total minutes logged, based on year and all years.
     *
     * @param int $year
     * @param int $userId
     * @return Illuminate\Support\Collection
     */
    public function getHoursPerSkill(int $year = null, int $userId): Collection
    {
        $queryBuilder = $this->missionSkill->select([
            'mission_skill.skill_id',
            'skill.skill_name',
            \DB::raw('sum(minute(timesheet.time) + (hour(timesheet.time)*60)) as total_minutes')
        ])
        ->leftjoin('mission', 'mission.mission_id', 'mission_skill.mission_id')
        ->leftjoin('skill', 'skill.skill_id', 'mission_skill.skill_id')
        ->leftjoin('timesheet', 'mission.mission_id', 'timesheet.mission_id')
        ->where('mission.mission_type', 'TIME');
        if (!empty($year)) {
            $queryBuilder = $queryBuilder->whereRaw(\DB::raw('year(timesheet.created_at) = "'.$year.'"'));
        }
        $queryBuilder = $queryBuilder->where('mission.publication_status', 'APPROVED')
        ->where('timesheet.user_id', $userId)
        ->whereNotNull('mission.mission_id')
        ->whereIn('timesheet.status_id', $this->timesheetStatus->getApprovedStatuses()->toArray())
        ->whereNotNull('timesheet.timesheet_id')
        ->groupBy('mission_skill.skill_id');
        
        return $queryBuilder->get();
    }
}
