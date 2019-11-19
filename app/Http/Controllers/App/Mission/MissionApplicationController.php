<?php
namespace App\Http\Controllers\App\Mission;

use App\Http\Controllers\Controller;
use Bschmitt\Amqp\Amqp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Repositories\Mission\MissionRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Events\User\UserActivityLogEvent;
use App\Helpers\Helpers;

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
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new mission application controller instance.
     *
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        MissionApplicationRepository $missionApplicationRepository,
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers
    ) {
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
    }

    /**
     * Apply to mission
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missionApplication(Request $request): JsonResponse
    {
        $missionStatus = config("constants.publication_status")["APPROVED"];
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "mission_id" => [
                    "integer",
                    "required",
                    "exists:mission,mission_id,deleted_at,NULL,publication_status,".$missionStatus
                ],
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


        // Send data of the new mission application created to Optimy app using "volunteerApplications" queue from RabbitMQ
        // THIS IS REALLY DIRTY CODE, but we have no choice for the moment for getting the tenant_id.
        // A service who will implement that will be truly appreciated:
        $domain = $this->helpers->getSubDomainFromRequest($request);
        $this->helpers->switchDatabaseConnection('mysql', $request);
        $db = app()->make('db');
        $tenant = $db->table('tenant')->select('tenant_id')
                     ->where('name', $domain)->whereNull('deleted_at')->first();
        $this->helpers->switchDatabaseConnection('tenant', $request);
        //END DIRTY CODE

        $missionForOptimy = [
            'tenant_id' => $tenant->tenant_id,
            'mission_application_id' => $missionApplication->mission_application_id,
            'user_id' => $missionApplication->user_id,
            'mission_id' => $missionApplication->mission_id,
            'applied_at' => $missionApplication->applied_at,
            'approval_status' => $missionApplication->approval_status
        ];
        (new Amqp)->publish('volunteerApplications', json_encode($missionForOptimy) , ['queue' => 'volunteerApplications']);

        // Set response data
        $apiData = ['mission_application_id' => $missionApplication->mission_application_id];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_APPLICATION_CREATED');

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.MISSION'),
            config('constants.activity_log_actions.MISSION_APPLICATION_CREATED'),
            config('constants.activity_log_user_types.REGULAR'),
            $request->auth->email,
            get_class($this),
            $request->toArray(),
            $request->auth->user_id,
            $missionApplication->mission_application_id
        ));
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

            // Get default user avatar
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $defaultAvatar = $this->helpers->getUserDefaultProfileImage($tenantName);

            foreach ($missionVolunteers as $volunteers) {
                if (!isset($volunteers->avatar)) {
                    $volunteers->avatar = $defaultAvatar;
                }
            }

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
