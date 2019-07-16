<?php
namespace App\Repositories\MissionInvite;

use App\Repositories\MissionInvite\MissionInviteInterface;
use App\Helpers\ResponseHelper;
use App\Models\MissionInvite;

class MissionInviteRepository implements MissionInviteInterface
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Models\MissionInvite
     */
    public $missionInvite;
    
    /**
     * Create a new MissionInvite repository instance.
     *
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\MissionInvite $missionInvite
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        MissionInvite $missionInvite
    ) {
        $this->responseHelper = $responseHelper;
        $this->missionInvite = $missionInvite;
    }

    /*
     * Check mission is already added or not.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return int
     */
    public function checkInviteMission(int $missionId, int $inviteUserId, int $fromUserId): int
    {
        $inviteCount = $this->missionInvite
        ->where(['mission_id' => $missionId, 'to_user_id' => $inviteUserId, 'from_user_id' => $fromUserId])
        ->count();
        return $inviteCount;
    }
    
    /*
     * Store a newly created resource into database
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return App\Models\MissionInvite
     */
    public function inviteMission(int $missionId, int $inviteUserId, int $fromUserId): MissionInvite
    {
        $invite = $this->missionInvite
        ->create(['mission_id' => $missionId, 'to_user_id' => $inviteUserId, 'from_user_id' => $fromUserId]);
        return $invite;
    }
}
