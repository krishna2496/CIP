<?php
namespace App\Http\Controllers\App\Mission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Repositories\Mission\MissionRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use Validator;

class MissionApplicationController extends Controller
{
    use RestExceptionHandlerTrait;
    
    /**
     * @var MissionApplicationRepository
     */
    private $missionApplicationRepository;
    
    /**
     * @var MissionRepository
     */
    private $missionRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new mission application controller instance.
     *
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionApplicationRepository $missionApplicationRepository,
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Apply to mission
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missionApplication(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "mission_id" => "integer|required|exists:mission,mission_id,deleted_at,NULL,publication_status,".config("constants.publication_status")["APPROVED"],
                "availability_id" => "integer|exists:availability,availability_id,deleted_at,NULL"
            ]
        );
        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_MISSION_APPLICATION_DATA'),
                $validator->errors()->first()
            );
        }

        $applicationCount = $this->missionApplicationRepository->checkApplyMission(
            $request->mission_id,
            $request->auth->user_id
        );
        if ($applicationCount > 0) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_APPLICATION_ALREADY_ADDED'),
                trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_ALREADY_ADDED')
            );
        }

        $seatAvailable = $this->missionRepository->checkAvailableSeats($request->mission_id);
        if ($seatAvailable === false) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_APPLICATION_SEATS_NOT_AVAILABLE'),
                trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_SEATS_NOT_AVAILABLE')
            );
        }

        $applicationDeadline = $this->missionRepository->checkMissionApplicationDeadline($request->mission_id);
        if (!$applicationDeadline) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_APPLICATION_DEADLINE_PASSED'),
                trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_DEADLINE_PASSED')
            );
        }

        // Create new mission application
        $missionApplication = $this->missionApplicationRepository->storeApplication(
            $request->all(),
            $request->auth->user_id
        );

        // Set response data
        $apiData = ['mission_application_id' => $missionApplication->mission_application_id];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_APPLICATION_CREATED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Get recent volunteers
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVolunteers(Request $request, int $missionId): JsonResponse
    {
        try {
            $missionVolunteers = $this->missionApplicationRepository->missionVolunteerDetail($request, $missionId);

            // Set response data
            $apiData = $missionVolunteers;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (count($missionVolunteers) > 0) ? trans('messages.success.MESSAGE_MISSION_VOLUNTEERS_LISTING')
            : trans('messages.success.MESSAGE_NO_MISSION_VOLUNTEERS_FOUND');
            
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
    }
}
