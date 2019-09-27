<?php
namespace App\Repositories\Mission;

use Illuminate\Http\Request;
use App\Models\MissionRating;
use App\Models\Mission;
use App\Models\FavouriteMission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\GoalMission;
use App\Models\MissionApplication;

interface MissionInterface
{
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Mission
     */
    public function store(Request $request): Mission;
    
    /**
     * Update resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return App\Models\Mission
     */
    public function update(Request $request, int $id): Mission;
  
    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\Mission
     */
    public function find(int $id): Mission;
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Add/remove mission to favourite.
     *
     * @param int $userId
     * @param int $missionId
     * @return null|App\Models\FavouriteMission
     */
    public function missionFavourite(int $userId, int $missionId): ?FavouriteMission;
    
    /**
     * Get mission name.
     *
     * @param int $missionId
     * @param int $languageId
     * @return string
     */
    public function getMissionName(int $missionId, $languageId): string;

    /**
     * Add/update mission rating.
     *
     * @param int $userId
     * @param array $request
     * @return App\Models\MissionRating
     */
    public function storeMissionRating(int $userId, array $request): MissionRating;

    /**
     * Display mission media.
     *
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMissionMedia(int $missionId): Collection;

    /**
     * Display listing of related mission.
     *
     * @param Illuminate\Http\Request $request
     * @param int $languageId
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedMissions(Request $request, int $languageId, int $missionId): Collection;

    /**
     * Get mission detail.
     *
     * @param Illuminate\Http\Request $request
     * @param int $languageId
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMissionDetail(Request $request, int $languageId, int $missionId): Collection;

    /**
     * Display a listing of mission.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionList(Request $request): LengthAwarePaginator;

    /**
     * Display a listing of mission.
     *
     * @param Illuminate\Http\Request $request
     * @param Array $userFilterData
     * @param int $languageId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMissions(Request $request, array $userFilterData, int $languageId): LengthAwarePaginator;

    /**
     * Display a Explore mission data.
     *
     * @param Illuminate\Http\Request $request
     * @param string $topFilterData
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function exploreMission(Request $request, string $topFilterParams): Collection;

    /**
     * Display mission filter data.
     *
     * @param Illuminate\Http\Request $request
     * @param string $filterParams
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function missionFilter(Request $request, string $filterParams): Collection;

    /**
     * Check seats are available or not.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkAvailableSeats(int $missionId): bool;

    /**
     * Check mission application deadline
     *
     * @param int $missionId
     * @return bool
     */
    public function checkMissionApplicationDeadline(int $missionId): bool;

    /** Get mission application details by mission id, user id and status
     *
     * @param int $missionId
     * @param int $userId
     * @param string $status
     * @return MissionApplication
     */
    public function getMissionApplication(int $missionId, int $userId, string $status): MissionApplication;
    
    /**
     * Get Mission data for timesheet
     *
     * @param int $id
     * @return App\Models\Mission
     */
    public function getTimesheetMissionData(int $id): Mission;
    
    /**
     * Get Mission type
     *
     * @param int $id
     * @return null|Collection
     */
    public function getMissionType(int $id): ?Collection;
}
