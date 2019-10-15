<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\Message\MessageInterface;
use Illuminate\Http\Request;

class MessageRepository implements MessageInterface
{
    /**
     *
     * @var App\Models\Message
     */
    private $message;

    /**
     * Create a new message repository instance.
     *
     * @param  App\Models\Message $message
     * @return void
     */
    public function __construct(
        Message $message
    ) {
        $this->message = $message;
    }

    /**
     * Store message details
     *
     * @param \Illuminate\Http\Request $request
     * @param int $sendMessageFrom
     * @return App\Models\Message
     */
    public function store(Request $request, $sendMessageFrom): Message
    {
        $messageDataArray = array(
            'user_id' => $request->auth->user_id,
            'sent_from' => $sendMessageFrom,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => 1,
            'is_anonymous' => 1
        );
        $messageData = $this->message->create($messageDataArray);
        return $messageData;
    }
}
