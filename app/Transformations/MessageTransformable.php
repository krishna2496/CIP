<?php
namespace App\Transformations;

use App\Models\Message;

trait MessageTransformable
{
    /**
     * Get Transfomed messages
     *
     * @param App\Models\Message $message
     * @return App\Models\Message
     */

    protected function transformMessage(Message $message):Message
    {
        $messageData = new Message;
        $messageData->message_id = (int) $message->message_id;
        $messageData->sent_from = $message->sent_from;
        $messageData->user_id = $message->user_id;
        $messageData->admin_name = $message->admin_name;
        $messageData->subject = $message->subject;
        $messageData->message = $message->message;
        $messageData->is_read = $message->is_read;
        $messageData->is_anonymous = $message->is_anonymous;
        
        if (!empty($message->user)) {
            $messageData->first_name = $message->user->first_name;
            $messageData->last_name = $message->user->last_name;
        }
        
        return $messageData;
    }
}
