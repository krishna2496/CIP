<?php
namespace App\Services\Dashboard;

class DashboardService
{
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
        $key = array_search($userId, array_column($allUsersTimesheetData, 'user_id'));
        if ($key === false) {
            $volunteeringRank = 0;
        }
        
        return $volunteeringRank;
    }

    /**
     * Get donated amount
     *
     */
    public function getDonatedAmount()
    {
        return 55897.78;
    }

    /**
     * Get contribution count
     *
     */
    public function getContributionCount()
    {
        return 89;
    }

    /**
     * Get matched
     *
     */
    public function getMatched()
    {
        return 2548;
    }

    /**
     * Get payroll donation
     *
     */
    public function getPayrollDonation()
    {
        return 254.78;
    }

    /**
     * Get volunteering grants
     *
     */
    public function getVolunteeringGrants()
    {
        return 150;
    }

    /**
     * Get tracked hours this year
     *
     */
    public function getTrackedHoursThisYears()
    {
        return [
            'completed_hours' => 792,
            'goal' => 500
        ];
    }
}
