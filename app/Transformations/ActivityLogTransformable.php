<?php
namespace App\Transformations;

use App\Models\ActivityLog;

trait ActivityLogTransformable
{
    /**
     * Get Transfomed stories
     *
     * @param App\Models\ActivityLog $activityLog
     * @param int $languageId
     * @param string $defaultAvatar
     * @return array
     */

    protected function transformActivityLog(ActivityLog $activityLog): array
    {
        $activityLogData = array();
        $activityLogData['activity_log_id'] = (int) $activityLog->activity_log_id;
        $activityLogData['type'] = $activityLog->type;
        $activityLogData['action'] = $activityLog->action;
        $activityLogData['object_class'] = $activityLog->object_class;
        $activityLogData['object_id'] = $activityLog->object_id;
        $activityLogData['object_value'] = @unserialize($activityLog->object_value) !== false ?
            unserialize($activityLog->object_value) : [];
        $activityLogData['date'] = $activityLog->date;
        $activityLogData['user_id'] = $activityLog->user_id;
        $activityLogData['user_type'] = $activityLog->user_type;
        $activityLogData['user_value'] = $activityLog->user_value;

        return $activityLogData;
    }
}
