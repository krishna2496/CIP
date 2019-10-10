<?php

namespace App\Repositories\StoryVisitor;

use App\Models\StoryVisitor;
use App\Models\Story;
use App\Repositories\StoryVisitor\StoryVisitorInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class StoryVisitorRepository implements StoryVisitorInterface
{
    /**
     *
     * @var App\Models\StoryVisitor
     */
    private $storyVisitor;

    /**
     * Create a new Story visitor repository instance.
     *
     * @param  App\Models\StoryVisitor $storyVisitor
     * @return void
     */
    public function __construct(
        StoryVisitor $storyVisitor
    ) {
        $this->storyVisitor = $storyVisitor;
    }

    /**
     * Update story view count by store story visitor data & return story view count
     *
     * @param App\Models\Story $story
     * @param integer $loginUserId
     * @return int $storyViewCount
     */
    public function updateStoryViewCount(Story $story, int $loginUserId): int
    {
        // not found same story user & login user & story status is published then only count story view
        if ($story->user_id!=$loginUserId && $story->status ==  config('constants.story_status.PUBLISHED')) {
            $storyVisitorDataArray = array(
                'story_id' => $story->story_id,
                'user_id' => $loginUserId,
            );
            $storyVisitorData = $this->storyVisitor->updateOrCreate($storyVisitorDataArray);
        }
        return $storyViewCount = $this->storyVisitor->where('story_id', $story->story_id)->count();
    }
}
