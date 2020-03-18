<?php

namespace App\Http\Controllers\Admin\Timesheet;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\ResponseHelper;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Timesheet\TimesheetRepository;
use Validator;
use Illuminate\Http\JsonResponse;
use App\Events\User\UserNotificationEvent;
use App\Events\User\UserActivityLogEvent;
use Illuminate\Validation\Rule;

//!  Timesheet controller
/*!
This controller is responsible for handling timesheet listing and update operations.
 */
class TimesheetController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;

    /**
     * @var App\Repositories\Timesheet\TimesheetRepository
     */
    private $timesheetRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var string
     */
    private $userApiKey;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        TimesheetRepository $timesheetRepository,
        ResponseHelper $responseHelper,
        Request $request
    ) {
        $this->userRepository = $userRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->responseHelper = $responseHelper;
        $this->userApiKey =$request->header('php-auth-user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $userId
     * @return Illuminate\Http\JsonResponse
     */
    public function index(int $userId, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->find($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }

        $userTimesheetData = $this->timesheetRepository->getUserTimesheet($userId, $request);
        foreach ($userTimesheetData as $userTimesheet) {
            if ($userTimesheet->missionLanguage) {
                $userTimesheet->setAttribute('title', $userTimesheet->missionLanguage[0]->title);
                unset($userTimesheet->missionLanguage);
            }
            $userTimesheet->setAppends([]);
        }

        $apiData = $userTimesheetData->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (!empty($apiData)) ?
        trans('messages.success.MESSAGE_TIMESHEET_ENTRIES_LISTING') :
        trans('messages.success.MESSAGE_NO_TIMESHEET_ENTRIES_FOUND');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Approve/decline timehseet entry
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $timesheetId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $timesheetId): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "status" => ["required",Rule::in(config('constants.timesheet_status'))]
                ]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            $this->timesheetRepository->find($timesheetId);
            $this->timesheetRepository->updateTimesheetStatus($request->status, $timesheetId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TIMESETTING_STATUS_UPDATED');
            $apiData = ['timesheet_id' => $timesheetId];

            // Send notification to user
            $timsheetDetails = $this->timesheetRepository->getDetailsOfTimesheetEntry($timesheetId);
            if ($timsheetDetails->mission->mission_type === config('constants.mission_type.TIME')) {
                $notificationType = config('constants.notification_type_keys.VOLUNTEERING_HOURS');
            } else {
                $notificationType = config('constants.notification_type_keys.VOLUNTEERING_GOALS');
            }
            $entityId = $timesheetId;
            $action = config('constants.notification_actions.'.$timsheetDetails->status);
            $userId = $timsheetDetails->user_id;
            
            event(new UserNotificationEvent($notificationType, $entityId, $action, $userId));
            
            // Make activity log
            $activityLogStatus = $request->status == config('constants.timesheet_status.APPROVED') ?
                config('constants.activity_log_actions.APPROVED'): config('constants.activity_log_actions.DECLINED');

            event(new UserActivityLogEvent(
                config('constants.activity_log_types.VOLUNTEERING_TIMESHEET'),
                $activityLogStatus,
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $request->toArray(),
                null,
                $timesheetId
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TIMESHEET_ENTRY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TIMESHEET_ENTRY_NOT_FOUND')
            );
        }
    }
}
