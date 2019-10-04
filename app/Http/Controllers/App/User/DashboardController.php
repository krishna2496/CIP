<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\ResponseHelper;
use App\Helpers\Helpers;
use App\Repositories\User\UserRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Repositories\TenantOption\TenantOptionRepository;

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
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;
        
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        TimesheetRepository $timesheetRepository,
        MissionApplicationRepository $missionApplicationRepository,
        TenantOptionRepository $tenantOptionRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers
    ) {
        $this->userRepository = $userRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
    }
    
    /**
     * Get dashboard statistics
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->auth->user_id;
        $year = ((!is_null($request->year)) && ($request->year != "")) ? $request->year : (int) date('Y');
        $month = ((!is_null($request->month)) && ($request->month != "")) ? $request->month : (int) date('m');
        $missionId = $request->mission_id ?? null;

        $timesheetData = $this->timesheetRepository->getTotalHours($userId, $year, $month);
        $timesheetCount = $this->timesheetRepository->getTotalPendingRequests($userId, $year, $month);
        $missionCount = $this->missionApplicationRepository->missionApplicationCount($userId, $year, $month);
        $organizationCount = $this->missionApplicationRepository->organizationCount($userId, $year, $month);
        $goalHours = $this->userRepository->getUserGoalHours($userId);
        $tenantGoalHours = $this->tenantOptionRepository->getOptionValueFromOptionName('goal_hours');
        $chartData = $this->timesheetRepository->getTotalHoursbyMonth($userId, $year, $missionId);
        $allUsersTimesheetData = $this->timesheetRepository->getUsersTotalHours($year, $month);
        $totalGoalHours = $this->timesheetRepository->getTotalHoursForYear($userId, $year);
        
        // For total hours
        $totalHours = 0;
        foreach ($timesheetData as $timesheet) {
            $totalHours += $timesheet['total_hours'];
        }

        // For hours tracked this year
        $totalGoals = 0;
        foreach ($totalGoalHours as $timesheetHours) {
            $totalGoals += $timesheetHours['total_hours'];
        }

        // for volunteering Rank
        $key = array_search($userId, array_column($allUsersTimesheetData, 'user_id'));
        $volunteeringRank = (count($allUsersTimesheetData) != 0) ? (($key+1) / count($allUsersTimesheetData)) * 100 : 0;
     
        $apiData['total_hours'] = $this->helpers->convertInReportTimeFormat($totalHours);
        $apiData['volunteering_rank'] = (int)$volunteeringRank;
        $apiData['open_volunteering_requests'] = $timesheetCount;
        $apiData['mission_count'] = $missionCount;
        $apiData['voted_missions'] = '';
        $apiData['organization_count'] = count($organizationCount);
        $apiData['total_goal_hours'] = (!is_null($goalHours)) ? $goalHours : $tenantGoalHours;
        $apiData['completed_goal_hours'] = (int)($totalGoals / 60);
        $apiData['chart'] = $chartData;
        
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_DASHBOARD_STATISTICS_LISTING');
        return $this->responseHelper->success(Response::HTTP_OK, $apiMessage, $apiData);
    }
}
