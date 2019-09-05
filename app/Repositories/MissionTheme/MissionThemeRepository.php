<?php
namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MissionTheme;
use App\Models\TimesheetStatus;
use Illuminate\Support\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

class MissionThemeRepository implements MissionThemeInterface
{
    /**
     * @var App\Models\MissionTheme
     */
    public $missionTheme;

    /**
     * @var App\Models\TimesheetStatus
     */
    public $timesheetStatus;
 
    /**
     * Create a new MissionTheme repository instance.
     *
     * @param  App\Models\MissionTheme $missionTheme
     * @param App\Models\TimesheetStatus $timesheetStatus
     * @return void
     */
    public function __construct(MissionTheme $missionTheme, TimesheetStatus $timesheetStatus)
    {
        $this->missionTheme = $missionTheme;
        $this->timesheetStatus = $timesheetStatus;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $theme_id
     * @return Illuminate\Support\Collection
     */
    public function missionThemeList(Request $request, String $theme_id = ''): Collection
    {
        $themeQuery = $this->missionTheme->select('mission_theme_id', 'theme_name', 'translations');
        if ($theme_id != '') {
            $themeQuery->whereIn("mission_theme_id", explode(",", $theme_id));
        }
        $theme = $themeQuery->get();
        return $theme;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionThemeDetails(Request $request): LengthAwarePaginator
    {
        return $this->missionTheme->select('theme_name', 'mission_theme_id', 'translations')
        ->paginate($request->perPage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\MissionTheme
     */
    public function store(array $request): MissionTheme
    {
        return $this->missionTheme->create($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\Models\MissionTheme
     */
    public function update(array $request, int $id): MissionTheme
    {
        $missionTheme = $this->missionTheme->findOrFail($id);
        $missionTheme->update($request);
        return $missionTheme;
    }
    
    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\Models\MissionTheme
     */
    public function find(int $id): MissionTheme
    {
        return $this->missionTheme->findMissionTheme($id);
    }
    
    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->missionTheme->deleteMissionTheme($id);
    }

    /**
     * Get all theme history with total minutes logged, based on year and all years.
     *
     * @param int $year
     * @return Illuminate\Support\Collection
     */
    public function getHoursPerTheme(int $year = null): Collection
    {
        $queryBuilder = $this->missionTheme->select([
            'mission_theme.mission_theme_id',
            'mission_theme.theme_name',
            \DB::raw('sum(minute(time) + (hour(time)*60)) as total_minutes')
        ])
        ->leftjoin('mission', 'theme_id', 'mission_theme_id')
        ->leftjoin('timesheet', 'mission.mission_id', 'timesheet.mission_id')
        ->where('mission.mission_type', 'TIME');
        if (!empty($year)) {
            $queryBuilder = $queryBuilder->whereRaw(\DB::raw('year(timesheet.created_at) = "'.$year.'"'));
        }
        $queryBuilder = $queryBuilder->where('mission.publication_status', 'APPROVED')
        ->whereNotNull('mission.mission_id')
        ->whereIn('timesheet.status_id', $this->timesheetStatus->getApprovedStatuses()->toArray())
        ->whereNotNull('timesheet.timesheet_id')
        ->groupBy('mission_theme.mission_theme_id');
        
        return $queryBuilder->get();
    }
}
