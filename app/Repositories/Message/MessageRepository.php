<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\Message\MessageInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Display a listing of specified resources with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $sendFrom
     * @param int $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserMessages(
        Request $request,
        int $sendFrom,
        int $userId = null
    ): LengthAwarePaginator {
        $userMessageQuery = $this->message->where('sent_from', $sendFrom)
                            ->when(
                                $userId,
                                function ($query, $userId) {
                                    return $query->where('user_id', $userId);
                                }
                            )->orderBy('created_at', 'desc');
        return $userMessageQuery->paginate($request->perPage);
    }
}
