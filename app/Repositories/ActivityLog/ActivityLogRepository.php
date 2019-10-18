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
}
