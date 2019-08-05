<?php
namespace App\Repositories\MissionComment;

use App\Repositories\MissionComment\MissionCommentInterface;
use App\Models\Comment;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Collection;

class MissionCommentRepository implements MissionCommentInterface
{
    /**
     * @var App\Models\Comment
     */
    public $comment;
 
    /**
     * Create a new mission comment repository instance.
     *
     * @param  App\Models\Comment $comment
     * @param  App\Models\Mission $mission
     * @return void
     */
    public function __construct(Comment $comment, Mission $mission)
    {
        $this->comment = $comment;
        $this->mission = $mission;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param int $userId
     * @param array $request
     * @return App\Models\Comment
     */
    public function store(int $userId, array $request): Comment
    {
        $request['user_id'] = $userId;
        return $this->comment->create($request);
    }
    
    /**
     * Get mission comments.
     *
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getComments(int $missionId): Collection
    {
        $mission = $this->mission->findOrFail($missionId);
        $commentQuery = $mission->comment()
        ->where('approval_status', config("constants.comment_approval_status.PUBLISHED"))
        ->orderBy('comment_id', 'desc')
        ->with(['user:user_id,first_name,last_name,avatar']);
        return $commentQuery->take(config("constants.MISSION_COMMENT_LIMIT"))->get();
    }
}
