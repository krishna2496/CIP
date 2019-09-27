<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Repositories\User\UserRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\MissionApplication\MissionApplicationRepository;

class DashboardController extends Controller
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
     * @var App\Repositories\MissionApplication\MissionApplicationRepository
     */
    private $missionApplicationRepository;
        
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
     * @var App\Helpers\S3Helper
     */
    private $s3helper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        TimesheetRepository $timesheetRepository,
        MissionApplicationRepository $missionApplicationRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->userRepository = $userRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->auth->user_id;
        $timesheetData = $this->timesheetRepository->getTotalHours($userId);
        $timesheetCount = $this->timesheetRepository->getTotalPendingRequests($userId);
        $missionCount = $this->missionApplicationRepository->missionApplicationCount($userId);
        $organizationCount = $this->missionApplicationRepository->organizationCount($userId);
        
        $totalHours = 0;
        foreach ($timesheetData as $timesheet) {
            $totalHours += $timesheet['total_hours'];
        }

        $apiData['total_hours'] = $this->helpers->convertInReportTimeFormat($totalHours);
        $apiData['volunteering_rank'] = '';
        $apiData['open_volunteering_requests'] = $timesheetCount;
        $apiData['mission_count'] = $missionCount;
        $apiData['voted_missions'] = '';
        $apiData['organization_count'] = count($organizationCount);

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (empty($apiData)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
            : trans('messages.success.MESSAGE_USER_LISTING');
        return $this->responseHelper->success(Response::HTTP_OK, $apiMessage, $apiData);
    }
}
