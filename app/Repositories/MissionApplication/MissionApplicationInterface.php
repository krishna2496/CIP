<?php
namespace App\Repositories\MissionApplication;

use Illuminate\Http\Request;

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
     * Check seats are available or not.
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
}
