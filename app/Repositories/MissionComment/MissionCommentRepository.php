<?php
namespace App\Repositories\MissionComment;

use App\Repositories\MissionComment\MissionCommentInterface;
use App\Models\Comment;
use App\Models\Mission;
use Illuminate\Pagination\LengthAwarePaginator;

class MissionCommentRepository implements MissionCommentInterface
{
    /**
     * @var App\Models\Comment
     */
    private $comment;

    /**
     * @var App\Models\Mission
     */
    private $mission;
 
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
     * Store mission comment
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
     * Get mission comments
     *
     * @param int $missionId
     * @param array $statusList
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getComments(int $missionId, array $statusList = []): LengthAwarePaginator
    {
        $mission = $this->mission->findOrFail($missionId);
        
        $approvalStatusList = ($statusList) ? $statusList : [config("constants.comment_approval_status.PUBLISHED")];
            
        $commentQuery = $mission->comment()
        ->whereIn('approval_status', $approvalStatusList)
        ->orderBy('comment_id', 'desc')
        ->with(['user:user_id,first_name,last_name,avatar']);
        return $commentQuery->paginate(config("constants.MISSION_COMMENT_LIMIT"));
    }

    /**
     * Get comment detail
     *
     * @param int $commentId
     * @return App\Models\Comment
     */
    public function getComment(int $commentId): Comment
    {
        return $this->comment->findOrFail($commentId);
    }

    /**
     * Update comment, by commentId
     *
     * @param int $commentId
     * @param array $data
     * @return App\Models\Comment
     */
    public function updateComment(int $commentId, array $data): Comment
    {
        $comment = $this->comment->findOrFail($commentId);
        $comment->update($data);
        return $comment;
    }

    /**
     * Delete comment, by commentId
     *
     * @param int $commentId
     * @return bool
     */
    public function deleteComment(int $commentId): bool
    {
        $comment = $this->comment->findOrFail($commentId);
        return $comment->delete();
    }
}
