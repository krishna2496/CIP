<?php
namespace App\Repositories\MissionInvite;

use Illuminate\Http\Request;

interface MissionInviteInterface
{
    /**
     * Display a mission ratings.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return float
     */
    public function checkInviteMission(int $missionId, int $inviteUserId, int $fromUserId);

    /**
     * Display a mission ratings.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return MissionInvite
     */
    public function inviteMission(int $missionId, int $inviteUserId, int $fromUserId);
}
