<?php
namespace App\Repositories\StoryVisitor;

use App\Models\StoryVisitor;
use Illuminate\Http\Request;

interface StoryVisitorInterface
{
    /**
     * Store story visitor details
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $storyId
     * @return App\Models\StoryVisitor;
     */
    public function store(Request $request, int $storyId): StoryVisitor;
}
