<?php
namespace App\Http\Controllers\Admin\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use App\Events\User\UserActivityLogEvent;
use App\Events\User\UserNotificationEvent;

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
     * Create a new mission application controller instance.
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
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function missionApplications(Request $request, int $missionId): JsonResponse
    {
        try {
            $applicationList = $this->missionApplicationRepository->missionApplications($request, $missionId);
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATIONS_LISTING')
             : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return $this->responseHelper->successWithPagination(
                Response::HTTP_OK,
                $responseMessage,
                $applicationList
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        }
    }

    /**
     * Display specified resource.
     *
     * @param int $missionId
     * @param int $applicationId
     * @return Illuminate\Http\JsonResponse
     */
    public function missionApplication(int $missionId, int $applicationId): JsonResponse
    {
        $applicationList = $this->missionApplicationRepository->missionApplication($missionId, $applicationId);
        $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING')
            : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
        
        return $this->responseHelper->success(Response::HTTP_OK, $responseMessage, $applicationList);
    }

    /**
     * Update mission application
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @param int $applicationId
     * @return Illuminate\Http\JsonResponse
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'approval_status' => ['required',Rule::in(config('constants.application_status'))],
        ]);

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_MISSION_APPLICATION_DATA'),
                $validator->errors()->first()
            );
        }

        try {
            $application = $this->missionApplicationRepository->updateApplication(
                $request,
                $missionId,
                $applicationId
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                $e->getMessage()
            );
        }
        
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_APPLICATION_UPDATED');
        
        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.MISSION'),
            config('constants.activity_log_actions.MISSION_APPLICATION_STATUS_CHANGED'),
            config('constants.activity_log_user_types.API'),
            $request->header('php-auth-user'),
            get_class($this),
            $request->toArray(),
            null,
            $missionId
        ));
        // Send notification to user
        $notificationType = config('constants.notification_type_keys.MISSION_APPLICATION');
        $entityId = $applicationId;
        $action = config('constants.notification_actions.'.$request->approval_status);
        $userId = $application->user_id;
        
        event(new UserNotificationEvent($notificationType, $entityId, $action, $userId));
        
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
