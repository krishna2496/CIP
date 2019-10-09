<?php

namespace App\Repositories\StoryVisitor;

use App\Models\StoryVisitor;
use App\Repositories\StoryVisitor\StoryVisitorInterface;
use Illuminate\Http\Request;

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
     * Store story visitor details
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $storyId
     * @return App\Models\StoryVisitor;
     */
    public function store(Request $request, int $storyId): StoryVisitor
    {
        $storyVisitorDataArray = array(
            'story_id' => $storyId,
            'user_id' => $request->auth->user_id,
        );

        $storyVisitorData = $this->storyVisitor->updateOrCreate($storyVisitorDataArray);
        return $storyVisitorData;
    }
}
