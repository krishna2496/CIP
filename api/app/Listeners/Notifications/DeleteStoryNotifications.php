<?php


namespace App\Listeners\Notifications;

use App\Events\Story\StoryDeletedEvent;
use App\Models\MissionApplication;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\Story;

class DeleteStoryNotifications
{
    /**
     * @param StoryDeletedEvent $event
     */
    public function handle(StoryDeletedEvent $event)
    {
        $storyIds = Story::withTrashed()
            ->where('story_id', '=', $event->storyId)
            ->get('story_id')
            ->map(function (Story $story) {
                return $story->story_id;
            })
            ->toArray();

        $notificationTypeId = NotificationType::where(['notification_type' => config("constants.notification_type_keys")["MY_STORIES"]])
            ->get('notification_type_id')
            ->first()
            ->notification_type_id;

        Notification::where(['notification_type_id' => $notificationTypeId])
            ->whereIn('entity_id', $storyIds)
            ->delete();
    }
}
