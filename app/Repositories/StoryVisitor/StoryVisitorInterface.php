<?php
namespace App\Repositories\StoryVisitor;

use App\Models\StoryVisitor;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface StoryVisitorInterface
{
    /**
     * Update story view count by store story visitor data & return story view count
     *
     * @param App\Models\Story $story
     * @param integer $loginUserId
     * @return int $storyViewCount
     */
    public function updateStoryViewCount(Story $story, int $loginUserId): int;
}
