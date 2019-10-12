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
    * @return App\Models\Message
    */
    public function store(Request $request, $sendMessageFrom): Message;

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
    ): LengthAwarePaginator;
}
