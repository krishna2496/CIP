<?php
namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class MissionApplicationController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionApplicationRepository
     */
    private $missionApplicationRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionApplicationRepository $missionApplicationRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Apply to a mission
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missionApplication(Request $request): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "mission_id" => "required|exists:mission,mission_id",
                    "availability_id" => "required|exists:user_availability,availability_id"
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

            $applyCount = $this->missionApplicationRepository->checkApplyMission(
                $request->mission_id,
                $request->auth->user_id
            );
            if ($applyCount > 0) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_MISSION_APPLICATION_ALREADY_ADDED'),
                    trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_ALREADY_ADDED')
                );
            }

            $available = $this->missionApplicationRepository->checkAvailableSeats($request->mission_id);
            if ($available == false) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_MISSION_APPLICATION_SEATS_NOT_AVAILABLE'),
                    trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_SEATS_NOT_AVAILABLE')
                );
            }

            $deadline = $this->missionApplicationRepository->checkMissionDeadline($request->mission_id);
            if ($deadline == false) {
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
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
