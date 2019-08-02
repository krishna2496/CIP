<?php
namespace App\Repositories\MissionApplication;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionApplication\MissionApplicationInterface;
use App\Helpers\ResponseHelper;
use App\Models\MissionApplication;
use App\Models\TimeMission;
use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @var App\Models\TimeMission
     */
    public $timeMission;
   
    /**
     * Create a new MissionApplication repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\TimeMission $timeMission
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\MissionApplication $missionApplication
     * @return void
     */
    public function __construct(
        Mission $mission,
        TimeMission $timeMission,
        ResponseHelper $responseHelper,
        MissionApplication $missionApplication
    ) {
        $this->mission = $mission;
        $this->timeMission = $timeMission;
        $this->responseHelper = $responseHelper;
        $this->missionApplication = $missionApplication;
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
        return $this->missionApplication->checkApplyMission($missionId, $userId);
    }

    /**
     * Add mission application.
     *
     * @param array $request
     * @param int $userId
     * @return App\Models\MissionApplication
     */
    public function storeApplication(array $request, int $userId): MissionApplication
    {
        $application = array(
            'mission_id' => $request['mission_id'],
            'user_id' => $userId,
            'motivation' => $request['motivation'] ?? '',
            'availability_id' => $request['availability_id'],
            'approval_status' => config('constants.application_status.PENDING'),
            'applied_at' => Carbon::now()
        );
        return $this->missionApplication->create($application);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionApplications(Request $request, int $missionId): LengthAwarePaginator
    {
        return $this->missionApplication->find($request, $missionId);
    }

    /**
     * Display specified resource.
     *
     * @param int $missionId
     * @param int $applicationId
     * @return array
     */
    public function missionApplication(int $missionId, int $applicationId): array
    {
        return $this->missionApplication->findDetail($missionId, $applicationId);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $missionId
     * @param int $applicationId
     * @return App\Models\MissionApplication
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId): MissionApplication
    {
        try {
            $this->mission->findOrFail($missionId);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
        $missionApplication = $this->missionApplication->findOrFail($applicationId);
        $missionApplication->update($request->toArray());
        return $missionApplication;
    }

    /**
     * Get recent volunteers
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionVolunteerDetail(Request $request, int $missionId): LengthAwarePaginator
    {
        $this->mission->findOrFail($missionId);
        return $this->missionApplication->getVolunteers($request, $missionId);
    }
}
