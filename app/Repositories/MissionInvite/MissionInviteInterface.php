<?php
namespace App\Repositories\MissionInvite;

use Illuminate\Http\Request;

interface MissionInviteInterface
{
    /**
     * Check already invited for mission or not.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return float
     */
    public function checkInviteMission(int $missionId, int $inviteUserId, int $fromUserId);

    /**
     * Invite for a mission.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return MissionInvite
     */
    public function inviteMission(int $missionId, int $inviteUserId, int $fromUserId);
}
