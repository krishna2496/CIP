<?php
namespace App\Repositories\MissionApplication;

use App\Repositories\MissionApplication\MissionApplicationInterface;
use App\Helpers\ResponseHelper;
use App\Models\MissionApplication;
use App\Models\TimeMission;
use App\Models\Mission;
use Carbon\Carbon;

class MissionApplicationRepository implements MissionApplicationInterface
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Models\MissionApplication
     */
    public $missionApplication;
   
    /**
     * Create a new MissionApplication repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\MissionApplication $missionApplication
     * @return void
     */
    public function __construct(
        Mission $mission,
        ResponseHelper $responseHelper,
        MissionApplication $missionApplication
    ) {
        $this->mission = $mission;
        $this->responseHelper = $responseHelper;
        $this->missionApplication = $missionApplication;
    }

    
    /*
     * Check seats are available or not.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkAvailableSeats(int $missionId): bool
    {
        $mission = $this->mission->select('*')
        ->where('mission.mission_id', $missionId)
        ->withCount(['missionApplication as mission_application_count' => function ($query) use ($missionId) {
            $query->where('approval_status', config("constants.application_status")["AUTOMATICALLY_APPROVED"]);
        }])->first();

        if ($mission['total_seats'] != 0) {
            $seatsLeft = ($mission['total_seats']) - ($mission['mission_application_count']);
            if ($seatsLeft == 0 || $mission['total_seats'] == $mission['mission_application_count']) {
                return false;
            }
        }
        return true;
    }
    
    /*
     * Check seats are available or not.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkMissionDeadline(int $missionId): bool
    {
        $mission = $this->mission->findOrFail($missionId);
        if ($mission->mission_type == config('constants.mission_type.TIME')) {
            $applicationDeadline = TimeMission::where('mission_id', $missionId)->value('application_deadline');
            return ($applicationDeadline > Carbon::now()) ? true : false;
        }
    }
    
    /*
     * Check already applied for a mission or not.
     *
     * @param int $missionId
     * @param int $userId
     * @return int
     */
    public function checkApplyMission(int $missionId, int $userId): int
    {
        return $applyCount = $this->missionApplication
        ->where(['mission_id' => $missionId, 'user_id' => $userId])
        ->count();
    }

    /**
     * Add mission application.
     *
     * @param array $request
     * @return App\Models\MissionApplication
     */
    public function storeApplication(array $request, int $userId): MissionApplication
    {
        $application = array(
            'mission_id' => $request['mission_id'],
            'user_id' => $userId,
            'motivation' => $request['motivation'],
            'availability_id' => $request['availability_id']
        );
        return $this->missionApplication->create($application);
    }
}
