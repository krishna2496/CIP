<?php
namespace App\Repositories\Message;

use App\Models\Message;
use Illuminate\Http\Request;

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
}
