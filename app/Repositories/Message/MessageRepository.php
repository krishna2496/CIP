<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\Message\MessageInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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
     * @return null|int messageId
     */
    public function store(Request $request, int $sendMessageFrom): ?int
    {
        $adminName =  !empty($request->admin) ? $request->admin : null;
        $isAnonymous = !empty($request->admin) ?
                       config('constants.message.not_anonymous_name') :
                       config('constants.message.anonymous_name');

        // found message from admin
        if ($sendMessageFrom==config('constants.message.send_message_from.admin')) {
            $now = Carbon::now()->toDateTimeString();
            foreach ($request->user_ids as $userId) {
                $messageDataArray [] = [
                    'user_id' => $userId,
                    'sent_from' => $sendMessageFrom,
                    'admin_name' => $adminName,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'is_read' => config('constants.message.unread'),
                    'is_anonymous' => $isAnonymous,
                    'created_at'=>$now,
                    'updated_at'=>$now,
                ];
            }
            
            $messageData = $this->message->insert($messageDataArray);
        } else {
            $messageDataArray = array(
                'user_id' => $request->auth->user_id,
                'sent_from' => $sendMessageFrom,
                'admin_name' => $adminName,
                'subject' => $request->subject,
                'message' => $request->message,
                'is_read' => config('constants.message.read'),
                'is_anonymous' => $isAnonymous,
            );
            $messageData = $this->message->create($messageDataArray);
        }

        return !empty($messageData->message_id) ? $messageData->message_id : null;
    }

    /**
     * Display a listing of specified resources with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $sentFrom
     * @param Array $userIds
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserMessages(
        Request $request,
        int $sentFrom,
        array $userIds = []
    ): LengthAwarePaginator {
        $userMessageQuery = $this->message->select('*')->with(['user' => function ($query) {
            $query->select('user_id', 'first_name', 'last_name');
        }])->where('sent_from', $sentFrom)
                            ->when(
                                $userIds,
                                function ($query, $userIds) {
                                    return $query->whereIn('user_id', $userIds);
                                }
                            )->orderBy('created_at', 'desc');

        return $userMessageQuery->paginate($request->perPage);
    }


    /**
     * Remove the message details.
     *
     * @param int $messageId
     * @param int $sentFrom
     * @param int $userId
     * @return bool
     */
    public function delete(int $messageId, int $sentFrom, int $userId = null): bool
    {
        return $this->message->where(
            [
                'message_id' => $messageId,
                'sent_from' => $sentFrom
            ]
        ) ->when(
            $userId,
            function ($query, $userId) {
                return $query->where('user_id', $userId);
            }
        )->firstOrFail()->delete();
    }
}
