<?php
namespace App\Repositories\Story;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Collection;

interface StoryInterface
{
    /**
     * Store story details
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Story
     */
    public function store(Request $request): Story;

    /**
     * Update story details
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @return App\Models\Story
     */
    public function update(Request $request, int $storyId): Story;
    
    /**
    * Remove the story details.
    *
    * @param  int  $storyId
    * @param  int  $userId
    * @return bool
    */
    public function delete(int $storyId, int $userId): bool;

    /**
    * Store story images.
    *
    * @param \Illuminate\Http\Request $request
    * @param int $storyId
    * @return void
    */
    public function storeStoryImages(Request $request, int $storyId): void;

    /**
     * Store story video url.
     *
     * @param array $videoUrls
     * @param int $storyId
     * @return void
     */
    public function storeStoryVideoUrl(array $videoUrls, int $storyId): void;

    /**
     * Fetch story details
     *
     * @param int $userId
     * @param int $storyId
     * @param array $storyStatus
     *
     * @return null|Illuminate\Support\Collection
     */
    public function getStoryDetails(int $userId, int $storyId, array $storyStatus): ?Collection;

}
