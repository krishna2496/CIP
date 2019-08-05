<?php
namespace App\Repositories\MissionComment;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

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
     * Get mission comments.
     *
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getComments(int $missionId): Collection;
}
