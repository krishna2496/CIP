<?php
namespace App\Repositories\MissionApplication;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MissionApplicationInterface
{
    /*
     * Check seats are available or not.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkAvailableSeats(int $missionId);

    /*
     * Check mission deadline.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkMissionDeadline(int $missionId);

    /*
     * Check already applied for a mission or not.
     *
     * @param int $missionId
     * @param int $userId
     * @return int
     */
    public function checkApplyMission(int $missionId, int $userId);
    
    /**
     * Add mission application.
     *
     * @param array $request
     * @return App\Models\MissionApplication
     */
    public function storeApplication(array $request, int $userId);

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionApplications(Request $request, int $missionId);

    /**
     * Display specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @param int $applicationId
     * @return array
     */
    public function missionApplication(int $missionId, int $applicationId);

    /**
     * Update resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @param int $applicationId
     * @return App\Models\MissionApplication
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId);

    /*
     * Get recent volunteers
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionVolunteerDetail(Request $request, int $missionId): LengthAwarePaginator;
}
