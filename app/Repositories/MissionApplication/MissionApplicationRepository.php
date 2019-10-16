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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $missionApplication = $this->missionApplication->findOrFail($applicationId);
            $missionApplication->update($request->toArray());
            return $missionApplication;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(
                trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_NOT_FOUND')
            );
        }
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

    /**
     * Get mission application count.
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return null|int
     */
    public function missionApplicationCount(int $userId, int $year, int $month): ?int
    {
        return $this->missionApplication->missionApplicationCount($userId, $year, $month);
    }

    /**
     * Get organization count.
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return null|array
     */
    public function organizationCount(int $userId, int $year, int $month): ?array
    {
        return $this->mission
        ->leftJoin('mission_application', 'mission_application.mission_id', '=', 'mission.mission_id')
        ->where(['mission_application.user_id' => $userId])
        ->where('mission_application.approval_status', '<>', config('constants.application_status.REFUSED'))
        ->whereYear('applied_at', $year)
        ->whereMonth('applied_at', $month)
        ->groupBy('mission.organisation_id')
        ->get()->toArray();
    }

    /**
     * Get pending application count.
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return null|int
     */
    public function pendingApplicationCount(int $userId, int $year, int $month): ?int
    {
        return $this->missionApplication->pendingApplicationCount($userId, $year, $month);
    }
}
