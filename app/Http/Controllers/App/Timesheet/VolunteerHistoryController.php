<?php

namespace App\Http\Controllers\App\Timesheet;

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
        ResponseHelper $responseHelper
    ) {
        $this->timesheetRepository = $timesheetRepository;
        $this->missionThemeRepository = $missionThemeRepository;
        $this->missionSkillRepository = $missionSkillRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
