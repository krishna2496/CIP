<?php

namespace App\Events\User;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationType;

class UserActivityLogEvent extends Event
{
    use SerializesModels;

    /**
     * @var array
     */
    public $activityDataArray;


    /**
     * Create a new event instance.
     *
     * @param string $notificationType
     * @param int $entityId
     * @param string $action
     * @param int|null $userId
     * @return void
     */
    public function __construct(
        $type,
        $action,
        $userType,
        $userValue,
        $objectClass = null,
        $objectValue = null,
        $userId = null,
        $objectId = null
    ) {
        $this->activityDataArray['type'] = $type;
        $this->activityDataArray['action'] = $action;
        $this->activityDataArray['user_type'] = $userType;
        $this->activityDataArray['user_value'] = $userValue;
        $this->activityDataArray['object_class'] = $objectClass;
        $this->activityDataArray['object_id'] = $objectId;
        $this->activityDataArray['object_value'] = $objectValue;
        $this->activityDataArray['user_id'] = $userId;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
