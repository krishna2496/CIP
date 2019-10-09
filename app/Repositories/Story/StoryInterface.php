<?php
namespace App\Repositories\Story;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * Display a user story listing with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $languageId
     * @param int $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserStoriesWithPagination(Request $request, int $languageId, int $userId): LengthAwarePaginator;

    /**
     * Get story details.
     *
     * @param int $storyId
     * @param string $storyStatus
     * @return null|App\Models\Story
     */
    public function getStoryDetails(int $storyId, string $storyStatus = null): Story;

    /**
     * Update story status field value, based on story_id condition
     *
     * @param int $storyStatus
     * @param int $Id
     * @return bool
     */
    public function updateStoryStatus(string $storyStatus, int $storyId): bool;

    /**
     * Do copy of declined story data
     *
     * @param int $oldStoryId
     * @return int $newStoryId
     */
    public function createStoryCopy(int $oldStoryId): int;

    /**
     * Display a listing of specified resources without pagination.
     *
     * @param int $languageId
     * @param int $userId
     * @return Object
     */
    public function getUserStories(int $languageId, int $userId): Object;

    /**
     * Store story images.
     *
     * @param string $tenantName
     * @param int $storyId
     * @param array $storyImages
     * @param int $userId
     * @return void
     */
    public function storeStoryImages(
        string $tenantName,
        int $storyId,
        array $storyImages,
        int $userId
    ): void;

    /**
     * Store story videos url.
     *
     * @param string $storyVideosUrl
     * @param int $storyId
     * @return void
     */
    public function storeStoryVideoUrl(string $storyVideosUrl, int $storyId): void;

    /**
     * Check story status
     *
     * @param int $userId
     * @param int $storyId
     * @param array $storyStatus
     *
     * @return bool
     */
    public function checkStoryStatus(int $userId, int $storyId, array $storyStatus): bool;

    /**
     * Submit story details
     *
     * @param int $userId
     * @param int $storyId
     * @return App\Models\Story
     */
    public function submitStory(int $userId, int $storyId): Story;

    /**
     * Find story by user id
     *
     * @param int $userId
     * @param int $storyId
     * @return App\Models\Story
     */
    public function findStoryByUserId(int $userId, int $storyId): Story;
}
