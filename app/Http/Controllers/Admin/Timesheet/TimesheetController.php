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
use App\Models\TimesheetStatus;
use Illuminate\Http\JsonResponse;

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
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        TimesheetRepository $timesheetRepository,
        ResponseHelper $responseHelper
    ) {
        $this->userRepository = $userRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->responseHelper = $responseHelper;
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
                    "status_id" => "required|numeric|exists:timesheet_status,timesheet_status_id"
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
            $this->timesheetRepository->updateTimesheetStatus($request->status_id, $timesheetId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TIMESETTING_STATUS_UPDATED');
            $apiData = ['timesheet_id' => $timesheetId];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TIMESHEET_ENTRY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TIMESHEET_ENTRY_NOT_FOUND')
            );
        }
    }
}
