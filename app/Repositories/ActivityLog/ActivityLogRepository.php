<?php
namespace App\Repositories\ActivityLog;

use App\Models\ActivityLog;
use App\Repositories\ActivityLog\ActivityLogInterface;

class ActivityLogRepository implements ActivityLogInterface
{
    /**
     * @var App\Models\ActivityLog
     */
    public $activityLog;

    /**
     * Create a new ActivityLog repository instance.
     *
     * @param  App\Models\ActivityLog $activityLog
     * @return void
     */
    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Get ActivityLog type id
     *
     * @param string $type
     * @return void
     */
    public function storeActivityLog(array $data)
    {
        $this->activityLog->create($data);
    }

    /**
     * Display a listing of specified resources with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getActivityLogs(Request $request): LengthAwarePaginator
    {
        $type = $request->type;
        $action = $request->action;
        $userType = $request->user_type;
        $userIds = !empty($request->users) ? explode(',', $request->users) : null;
        $order = $request->order;

        $activityLogQuery = $this->activityLog->when($userIds, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })->when($status, function ($query, $status) {
            return $query->where('status', $status);
        });
        return $userStoryQuery->paginate($request->perPage);
    }
}
