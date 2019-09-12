<?php

namespace App\Http\Controllers\App\VolunteerHistory;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\Mission\MissionRepository;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use App\Repositories\MissionTheme\MissionThemeRepository;
use App\Repositories\MissionSkill\MissionSkillRepository;
use App\Helpers\LanguageHelper;
use App\Helpers\ExportCSV;
use Carbon\Carbon;
use App\Helpers\Helpers;

class VolunteerHistoryController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Timesheet\TimesheetRepository
     */
    private $timesheetRepository;

    /**
     * @var App\Repositories\MissionTheme\MissionThemeRepository
     */
    private $missionThemeRepository;

    /**
     * @var App\Repositories\MissionSkill\MissionSkillRepository
     */
    private $missionSkillRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param App\Repositories\MissionTheme\MissionThemeRepository $missionThemeRepository
     * @param App\Repositories\MissionSkill\MissionSkillRepository $missionSkillRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     *
     * @return void
     */
    public function __construct(
        TimesheetRepository $timesheetRepository,
        MissionThemeRepository $missionThemeRepository,
        MissionSkillRepository $missionSkillRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->timesheetRepository = $timesheetRepository;
        $this->missionThemeRepository = $missionThemeRepository;
        $this->missionSkillRepository = $missionSkillRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
    }

    /**
     * Get all themes history with total minutes logged, based on year and all years.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function themeHistory(Request $request): JsonResponse
    {
        try {
            $userId = $request->auth->user_id;
            $themeTimeHistory = $this->missionThemeRepository->getHoursPerTheme($request->year, $userId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = (!empty($themeTimeHistory->toArray())) ?
            trans('messages.success.MESSAGE_THEME_HISTORY_PER_HOUR_LISTED'):
            trans('messages.success.MESSAGE_THEME_HISTORY_NOT_FOUND');
            $apiData = $themeTimeHistory->toArray();
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get all skill history with total minutes logged, based on year and all years.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function skillHistory(Request $request): JsonResponse
    {
        try {
            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $languageCode = $languages->where('code', $language)->first()->code;

            $userId = $request->auth->user_id;
            $skillTimeHistory = $this->missionSkillRepository->getHoursPerSkill($request->year, $userId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage =  (!empty($skillTimeHistory->toArray())) ?
            trans('messages.success.MESSAGE_SKILL_HISTORY_PER_HOUR_LISTED'):
            trans('messages.success.MESSAGE_SKILL_HISTORY_NOT_FOUND');
            $apiData = $skillTimeHistory->toArray();

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get all user mission with total time entry for each mission
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function timeMissionHistory(Request $request): JsonResponse
    {
        try {
            $statusArray = [
                config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED'),
                config('constants.timesheet_status_id.APPROVED')
            ];

            $timeMissionList = $this->timesheetRepository->timeRequestList($request, $statusArray);

            $apiMessage = (count($timeMissionList) > 0) ?
            trans('messages.success.MESSAGE_TIME_MISSION_TIME_ENTRY_LISTED') :
            trans('messages.success.MESSAGE_NO_TIME_MISSION_TIME_ENTRY_FOUND');
            
            return $this->responseHelper->successWithPagination(Response::HTTP_OK, $apiMessage, $timeMissionList);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get all skill history with total minutes logged, based on year and all years.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function goalMissionHistory(Request $request): JsonResponse
    {
        try {
            $statusArray = [
                config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED'),
                config('constants.timesheet_status_id.APPROVED')
            ];

            $goalMissionList = $this->timesheetRepository->goalRequestList($request, $statusArray);

            $apiMessage = (count($goalMissionList) > 0) ?
            trans('messages.success.MESSAGE_GOAL_MISSION_TIME_ENTRY_LISTED') :
            trans('messages.success.MESSAGE_NO_GOAL_MISSION_TIME_ENTRY_FOUND');
            
            return $this->responseHelper->successWithPagination(Response::HTTP_OK, $apiMessage, $goalMissionList);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
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
    public function exportGoalMissionHistory(Request $request): JsonResponse
    {
        try {
            $statusArray = [
                config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED'),
                config('constants.timesheet_status_id.APPROVED')
            ];

            $goalMissionList = $this->timesheetRepository->goalRequestList($request, $statusArray, false);

            if ($goalMissionList->count()) {
                $fileName = config('constants.export_timesheet_file_names.GOAL_MISSION_HISTORY_XLSX');
        
                $excel = new ExportCSV($fileName);

                $headings = [
                    trans('messages.export_sheet_headings.MISSION_NAME'),
                    trans('messages.export_sheet_headings.ORGANIZATION_NAME'),
                    trans('messages.export_sheet_headings.ACTIONS')
                ];

                $excel->setHeadlines($headings);

                foreach ($goalMissionList as $mission) {
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
            $apiMessage =  ($goalMissionList->count()) ?
                trans('messages.success.MESSAGE_USER_GOAL_MISSION_HISTORY_EXPORTED'):
                trans('messages.success.MESSAGE_ENABLE_TO_EXPORT_USER_GOAL_MISSION_HISTORY');
            $apiData = ($goalMissionList->count()) ? ['path' => $path] : [];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Export user's time mission history
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function exportTimeMissionHistory(Request $request): JsonResponse
    {
        try {
            $statusArray = [
                config('constants.timesheet_status_id.AUTOMATICALLY_APPROVED'),
                config('constants.timesheet_status_id.APPROVED')
            ];

            $timeRequestList = $this->timesheetRepository->timeRequestList($request, $statusArray, false);

            if ($timeRequestList->count()) {
                $fileName = config('constants.export_timesheet_file_names.TIME_MISSION_HISTORY_XLSX');
            
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
            trans('messages.success.MESSAGE_USER_TIME_MISSION_HISTORY_EXPORTED'):
            trans('messages.success.MESSAGE_ENABLE_TO_EXPORT_USER_TIME_MISSION_HISTORY');
            $apiData = ($timeRequestList->count()) ? ['path' => $path] : [];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
