<?php
namespace App\Repositories\ActivityLog;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;

interface ActivityLogInterface
{
    /**
     * Store activity data into database
     *
     * @param array $data
     * @return array
     */
    public function storeActivityLog(array $data);
}
