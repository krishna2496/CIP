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
     * Create a new controller instance.
     *
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param App\Repositories\MissionTheme\MissionThemeRepository $missionThemeRepository
     * @param App\Repositories\MissionSkill\MissionSkillRepository $missionSkillRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TimesheetRepository $timesheetRepository,
        MissionThemeRepository $missionThemeRepository,
        MissionSkillRepository $missionSkillRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper
    ) {
        $this->timesheetRepository = $timesheetRepository;
        $this->missionThemeRepository = $missionThemeRepository;
        $this->missionSkillRepository = $missionSkillRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
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
}
