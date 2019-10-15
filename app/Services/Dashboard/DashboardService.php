<?php
namespace App\Services\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\TenantDomainNotFoundException;
use App\Exceptions\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helpers;
use Validator;
use App\Helpers\ResponseHelper;

class DashboardService
{
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(Helpers $helpers, ResponseHelper $responseHelper)
    {
        $this->helpers = $helpers;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Get volunteering rank
     *
     * @param array $allUsersTimesheetData
     * @return int
     */
    public function getvolunteeringRank(array $allUsersTimesheetData, int $userId)
    {
        $userRankArray = array();
        $userTimesheetMinutes = 0;
        foreach ($allUsersTimesheetData as $allUsersTimesheet) {
            array_push($userRankArray, $allUsersTimesheet['total_minutes']);
            if ($userId == $allUsersTimesheet['user_id']) {
                $userTimesheetMinutes = $allUsersTimesheet['total_minutes'];
            }
        }
        $userRank = array_values(array_unique($userRankArray));
        $userRankIndex = array_search($userTimesheetMinutes, $userRank);
        $volunteeringRank = (count($allUsersTimesheetData) !== 0) ?
        (100/count($allUsersTimesheetData)) * ($userRankIndex+1) : 0;
        return $volunteeringRank;
    }
}
