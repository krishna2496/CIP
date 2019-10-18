<?php

namespace App\Events\User;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationType;

class UserActivityLogEvent extends Event
{
    use SerializesModels;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $object_class;

    /**
     * @var int|null
     */
    public $object_id = null;

    /**
     * @var string|null
     */
    public $object_value = null;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var string
     */
    public $user_type;

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
        $userId,
        $userType,
        $objectClass = null,
        $objectValue = null,
        $objectId = null
    ) {
        $notificationTypeDetails = NotificationType::where('notification_type', $notificationType)->first();
        
        $this->notificationTypeId = $notificationTypeDetails->notification_type_id;
        $this->entityId = $entityId;
        $this->action = $action;
        $this->userId = $userId;
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
