<?php
namespace App\Repositories\Message;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MessageInterface
{
    /**
     * Store message details
     *
     * @param \Illuminate\Http\Request $request
     * @param int $sendMessageFrom
     * @return null|int
     */
    public function store(Request $request, int $sendMessageFrom): ?int;

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
    ): LengthAwarePaginator;

    /**
     * Remove message details.
     *
     * @param int $messageId
     * @param int $sentFrom
     * @param int $userId
     * @return bool
     */
    public function delete(int $messageId, int $sentFrom, int $userId): bool;
      
    /**
     * Read message.
     *
     * @param int $messageId
     * @param int $userId
     * @param int $sentFrom
     * @return App\Models\Message
     */
    public function readMessage(int $messageId, int $userId, int $sentFrom): Message;
}
