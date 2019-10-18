<?php
namespace App\Transformations;

use App\Models\Message;
use Carbon\Carbon;

trait MessageTransformable
{
    /**
     * Get Transfomed messages
     *
     * @param Object $messages
     * @param int $messageUnreadCount
     * @return Array
     */

    protected function transformMessage(Object $messages, int $messageUnreadCount = null): Array
    {
        $messageData = array();
        foreach ($messages as $message) {
            $messageData['message_data'] [] = [
                'message_id' => (int) $message->message_id,
                'user_id' => $message->user_id,
                'admin_name' =>  $message->admin_name,
                'subject' =>  $message->subject,
                'message' => $message->message,
                'is_read' =>  $message->is_read,
                'is_anonymous' =>  $message->is_anonymous,
                'first_name' => !empty($message->user) ?  $message->user->first_name : null,
                'last_name' => !empty($message->user) ? $message->user->last_name : null,
                'created_at' => Carbon::parse($message->created_at)->format(config('constants.MESSAGE_DATE_FORMAT')),
            ];
        }
        
        if ($messageUnreadCount) {
            $messageData['count']['unread'] = $messageUnreadCount;
        }

        return $messageData;
    }
}
