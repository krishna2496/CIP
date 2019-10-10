<?php
namespace App\Repositories\StoryVisitor;

use App\Models\StoryVisitor;

interface StoryVisitorInterface
{
    /**
     * Update story view count per visitor & return story view count
     *
     * @param array $story
     * @param integer $userId
     * @return int $storyViewCount
     */
    public function updateStoryViewCount(array $story, int $loginUserId): int;
}
