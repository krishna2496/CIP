<?php

namespace App\Http\Controllers\App\Timesheet;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Traits\RestExceptionHandlerTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Validator;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Helpers\ExportCSV;
use App\Helpers\Helpers;

class TimesheetController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Timesheet\TimesheetRepository
     */
    private $timesheetRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    private $missionRepository;

    /**
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param App\Helpers\Helpers $helpers
     *
     * @return void
     */
    public function __construct(
        TimesheetRepository $timesheetRepository,
        ResponseHelper $responseHelper,
        MissionRepository $missionRepository,
        TenantOptionRepository $tenantOptionRepository,
        Helpers $helpers
    ) {
        $this->timesheetRepository = $timesheetRepository;
        $this->responseHelper = $responseHelper;
        $this->missionRepository = $missionRepository;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->helpers = $helpers;
    }

    /**
     * Get all timesheet entries
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $timesheetEntries = $this->timesheetRepository->getAllTimesheetEntries($request);

            $apiData = $timesheetEntries;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (count($timesheetEntries[config('constants.mission_type.TIME')]) > 0 ||
            count($timesheetEntries[config('constants.mission_type.GOAL')]) > 0) ?
            trans('messages.success.MESSAGE_TIMESHEET_ENTRIES_LISTING') :
            trans('messages.success.MESSAGE_NO_TIMESHEET_ENTRIES_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Store/Update timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $documentSizeLimit = config('constants.TIMESHEET_DOCUMENT_SIZE_LIMIT');
            $validator = Validator::make(
                $request->toArray(),
                [
                    'mission_id' => 'required|exists:mission,mission_id,deleted_at,NULL',
                    'date_volunteered' => 'required',
                    'day_volunteered' => ['required', Rule::in(config('constants.day_volunteered'))],
                    'documents.*' => 'max:' . $documentSizeLimit . '|valid_timesheet_document_type',
                ]
            );

            // If validator fails
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TIMESHEET_ITEMS_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            // Remove params
            $request->request->remove('status_id');

            try {
                // Fetch mission application data
                $missionApplicationData = $this->missionRepository->getMissionApplication(
                    $request->mission_id,
                    $request->auth->user_id
                );
            } catch (ModelNotFoundException $e) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVALID_DATA_FOR_TIMESHEET_ENTRY'),
                    trans('messages.custom_error_message.ERROR_INVALID_DATA_FOR_TIMESHEET_ENTRY')
                );
            }

            if ($missionApplicationData->approval_status
                != config('constants.timesheet_status.AUTOMATICALLY_APPROVED')) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.MISSION_APPLICATION_NOT_APPROVED'),
                    trans('messages.custom_error_message.MISSION_APPLICATION_NOT_APPROVED')
                );
            }

            // Fetch timesheet data
            $timesheetData = $this->timesheetRepository->getTimesheetDetails(
                $request->mission_id,
                $request->auth->user_id,
                $request->date_volunteered
            );
            $timesheetDetails = $timesheetData->toArray();
            if ($timesheetData->count() > 0) {
                $timesheetStatus = $timesheetDetails[0]["timesheet_status"]["status"];
              
                // If timesheet status is approved
                if ($timesheetStatus == config('constants.timesheet_status.APPROVED')
                    || $timesheetStatus == config('constants.timesheet_status.AUTOMATICALLY_APPROVED')) {
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_TIMESHEET_ALREADY_UPDATED'),
                        trans('messages.custom_error_message.ERROR_TIMESHEET_ALREADY_UPDATED')
                    );
                } else {
                    $request->request->add(['status_id' => config('constants.timesheet_status_id.PENDING')]);
                }
            }

            $dateTime = Carbon::createFromFormat('m-d-Y', $request->date_volunteered);
            $dateTime = strtotime($dateTime);
            $dateVolunteered = date('Y-m-d', $dateTime);
           
            // Fetch mission data from missionid
            $missionData = $this->missionRepository->find($request->mission_id);

            // Check mission type
            if ($missionData->mission_type == config('constants.mission_type.GOAL')) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        "action" => "required|integer|min:1",
                    ]
                );

                // Remove extra params
                $request->request->remove('hours');
                $request->request->remove('minutes');

                // Fetch goal objective from goal mission
                $objective = $this->missionRepository->getGoalObjective($request->mission_id);
                
                // Fetch all added goal actions from database
                $totalAddedActions = $this->timesheetRepository->getAddedActions($request->mission_id);

                // Add total actions
                $totalActions = $totalAddedActions + $request->action;

                // Check total goals are not maximum than provided goals
                if ($totalActions > $objective->goal_objective) {
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_INVALID_ACTION'),
                        trans('messages.custom_error_message.ERROR_INVALID_ACTION')
                    );
                }
            } else {
                $validator = Validator::make(
                    $request->all(),
                    [
                        "hours" => "required",
                        "minutes" => "required",
                    ]
                );

                $hours = intval($request->hours);
                if (($hours < 0) || ($hours > 23)) {
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_INVALID_HOURS'),
                        trans('messages.custom_error_message.ERROR_INVALID_HOURS')
                    );
                }
                $minutes = intval($request->minutes);
                if (($minutes < 0) || ($minutes > 59)) {
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_INVALID_MINUTES'),
                        trans('messages.custom_error_message.ERROR_INVALID_MINUTES')
                    );
                }

                $time = $request->hours . ":" . $request->minutes;
                $request->request->add(['time' => $time]);
                // Remove extra params
                $request->request->remove('action');

                // Check start dates and end dates of mission
                if ($missionData->start_date) {
                    $startDate = (new Carbon($missionData->start_date))->format('Y-m-d');
                    if ($dateVolunteered < $startDate) {
                        return $this->responseHelper->error(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                            config('constants.error_codes.ERROR_MISSION_STARTDATE'),
                            trans('messages.custom_error_message.ERROR_MISSION_STARTDATE')
                        );
                    } else {
                        if ($missionData->end_date) {
                            $endDate = (new Carbon($missionData->end_date))->format('Y-m-d');
                            if ($dateVolunteered > $endDate) {
                                $missionEndDate = Carbon::createFromFormat('Y-m-d', $endDate);
                       
                                // Fetch tenant options value
                                $tenantOptionData = $this->tenantOptionRepository
                                ->getOptionValue('ALLOW_TIMESHEET_ENTRY');

                                // Count records
                                if (count($tenantOptionData) > 0) {
                                    $tenantOptionDetails = $tenantOptionData->toArray();
                                    $extraWeeks = intval($tenantOptionDetails[0]['option_value']);
                           
                                    // Add weeks mission end date
                                    $totalDate = $missionEndDate->addWeeks($extraWeeks);
                                    if ($dateVolunteered > $totalDate) {
                                        return $this->responseHelper->error(
                                            Response::HTTP_UNPROCESSABLE_ENTITY,
                                            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                                            config('constants.error_codes.ERROR_MISSION_ENDDATE'),
                                            trans('messages.custom_error_message.ERROR_MISSION_ENDDATE')
                                        );
                                    }
                                } else {
                                    return $this->responseHelper->error(
                                        Response::HTTP_UNPROCESSABLE_ENTITY,
                                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                                        config('constants.error_codes.ERROR_MISSION_ENDDATE'),
                                        trans('messages.custom_error_message.ERROR_MISSION_ENDDATE')
                                    );
                                }
                            }
                        }
                    }
                }
            }

            // If validator fails
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TIMESHEET_ITEMS_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            // Remove white space from notes
            if ($request->has('notes')) {
                $notes = trim($request->notes);
                $request->request->add(['notes' => $notes]);
            }

            // Store timesheet item
            $request->request->add(['user_id' => $request->auth->user_id]);
            $timesheet = $this->timesheetRepository->storeOrUpdateTimesheet($request);

            // Set response data
            $apiStatus = ($timesheet->wasRecentlyCreated) ? Response::HTTP_CREATED : Response::HTTP_OK;
            $apiMessage = ($timesheet->wasRecentlyCreated) ? trans('messages.success.TIMESHEET_ENTRY_ADDED_SUCESSFULLY')
            : trans('messages.success.TIMESHEET_ENTRY_UPDATED_SUCESSFULLY');
            $apiData = ['timesheet_id' => $timesheet->timesheet_id];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Show timesheet data
     *
     * @param \Illuminate\Http\Request $request
     * @param int $timesheetId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function show(Request $request, int $timesheetId): JsonResponse
    {
        try {
            // Fetch timesheet data
            $timesheetData = $this->timesheetRepository->getTimesheetData($timesheetId, $request->auth->user_id);
            $timesheetDetail = $timesheetData->toArray();
            if ($timesheetData->time != null) {
                $time = explode(":", $timesheetData->time);
                $timesheetDetail += ["hours" => $time[0]];
                $timesheetDetail += ["minutes" => $time[1]];
                unset($timesheetDetail["time"]);
            }
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TIMESHEET_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $timesheetDetail);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.TIMESHEET_NOT_FOUND'),
                trans('messages.custom_error_message.TIMESHEET_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Remove the timesheet documents.
     *
     * @param \Illuminate\Http\Request $request
     * @param int  $timesheetId
     * @param int  $documentId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $timesheetId, int $documentId): JsonResponse
    {
        try {
            // Fetch timesheet data
            $timesheetData = $this->timesheetRepository->getTimesheetData($timesheetId, $request->auth->user_id);

            // Delete timesheet document
            try {
                $timesheetDocument = $this->timesheetRepository->delete($documentId, $timesheetId);
            } catch (ModelNotFoundException $e) {
                return $this->modelNotFound(
                    config('constants.error_codes.TIMESHEET_DOCUMENT_NOT_FOUND'),
                    trans('messages.custom_error_message.TIMESHEET_DOCUMENT_NOT_FOUND')
                );
            }

            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_TIMESHEET_DOCUMENT_DELETED');

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.TIMESHEET_NOT_FOUND'),
                trans('messages.custom_error_message.TIMESHEET_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Submit timesheet for approval
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitTimesheet(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->toArray(),
                [
                    'timesheet_entries' => 'required',
                    'timesheet_entries.*.timesheet_id' => 'required|exists:timesheet,timesheet_id,deleted_at,NULL',
                ]
            );

            // If validator fails
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_TIMESHEET_ITEMS_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            $timesheet = $this->timesheetRepository->submitTimesheet($request, $request->auth->user_id);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = (!$timesheet) ? trans('messages.success.TIMESHEET_ALREADY_SUBMITTED_FOR_APPROVAL') :
            trans('messages.success.TIMESHEET_SUBMITTED_SUCESSFULLY');

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.TIMESHEET_NOT_FOUND'),
                trans('messages.custom_error_message.TIMESHEET_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get Request timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingTimeRequests(Request $request): JsonResponse
    {
        try {
            $statusArray = [config('constants.timesheet_status_id.SUBMIT_FOR_APPROVAL')];
            $timeRequestList = $this->timesheetRepository->timeRequestList($request, $statusArray);
            
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (count($timeRequestList) > 0) ? trans('messages.success.MESSAGE_TIME_REQUEST_LISTING') :
            trans('messages.success.MESSAGE_TIME_REQUEST_NOT_FOUND');

            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $timeRequestList);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Fetch pending goal requests
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getPendingGoalRequests(Request $request): JsonResponse
    {
        try {
            $statusArray = [config('constants.timesheet_status_id.SUBMIT_FOR_APPROVAL')];
            $goalRequestList = $this->timesheetRepository->goalRequestList($request, $statusArray);

            $apiMessage = (count($goalRequestList) > 0) ? trans('messages.success.MESSAGE_GOAL_REQUEST_LISTING')
            : trans('messages.success.MESSAGE_NO_GOAL_REQUEST_FOUND');
            return $this->responseHelper->successWithPagination(Response::HTTP_OK, $apiMessage, $goalRequestList);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Export all pending time mission time entries.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function exportPendingTimeRequests(Request $request): JsonResponse
    {
        try {
            $statusArray = [config('constants.timesheet_status_id.SUBMIT_FOR_APPROVAL')];

            $timeRequestList = $this->timesheetRepository->timeRequestList($request, $statusArray, false);

            if ($timeRequestList->count()) {
                $fileName = config('constants.export_timesheet_file_names.PENDING_TIME_MISSION_ENTRIES_XLSX');
            
                $excel = new ExportCSV($fileName);

                $headings = [
                    trans('messages.export_sheet_headings.MISSION_NAME'),
                    trans('messages.export_sheet_headings.ORGANIZATION_NAME'),
                    trans('messages.export_sheet_headings.TIME'),
                    trans('messages.export_sheet_headings.HOURS')
                ];

                $excel->setHeadlines($headings);

                foreach ($timeRequestList as $mission) {
                    $excel->appendRow([
                        $mission->title,
                        $mission->organisation_name,
                        $mission->time,
                        $mission->hours
                    ]);
                }

                $tenantName = $this->helpers->getSubDomainFromRequest($request);

                $path = $excel->export('app/'.$tenantName.'/timesheet/'.$request->auth->user_id.'/exports');
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage =  ($timeRequestList->count()) ?
            trans('messages.success.MESSAGE_USER_PENDING_TIME_MISSION_ENTRIES_EXPORTED'):
            trans('messages.success.MESSAGE_ENABLE_TO_EXPORT_USER_PENDING_TIME_MISSION_ENTRIES');
            $apiData = ($timeRequestList->count()) ? ['path' => $path] : [];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Export user's goal mission history
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function exportPendingGoalRequests(Request $request): JsonResponse
    {
        try {
            $statusArray = [config('constants.timesheet_status_id.SUBMIT_FOR_APPROVAL')];
            $goalRequestList = $this->timesheetRepository->goalRequestList($request, $statusArray, false);
            
            if ($goalRequestList->count()) {
                $fileName = config('constants.export_timesheet_file_names.PENTIND_GOAL_MISSION_ENTRIES_XLSX');
        
                $excel = new ExportCSV($fileName);

                $headings = [
                    trans('messages.export_sheet_headings.MISSION_NAME'),
                    trans('messages.export_sheet_headings.ORGANIZATION_NAME'),
                    trans('messages.export_sheet_headings.ACTIONS')
                ];

                $excel->setHeadlines($headings);

                foreach ($goalRequestList as $mission) {
                    $excel->appendRow([
                        $mission->title,
                        $mission->organisation_name,
                        $mission->action
                    ]);
                }

                $tenantName = $this->helpers->getSubDomainFromRequest($request);
                
                $path = $excel->export('app/'.$tenantName.'/timesheet/'.$request->auth->user_id.'/exports');
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage =  ($goalRequestList->count()) ?
                trans('messages.success.MESSAGE_USER_PENDING_GOAL_MISSION_ENTRIES_EXPORTED'):
                trans('messages.success.MESSAGE_ENABLE_TO_EXPORT_USER_PENDING_GOAL_MISSION_ENTRIES');
            $apiData = ($goalRequestList->count()) ? ['path' => $path] : [];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
