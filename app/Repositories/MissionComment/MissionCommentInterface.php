<?php
namespace App\Repositories\MissionComment;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

interface MissionCommentInterface
{
    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @param int $userId
     * @return App\Models\Comment
     */
    public function store(int $userId, array $request): Comment;

    /**
     * Get mission comments
     *
     * @param int $missionId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getComments(int $missionId): LengthAwarePaginator;
}
