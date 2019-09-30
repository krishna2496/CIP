<?php
namespace App\Repositories\Story;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Database\Eloquent\Collection;
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
    * Remove the story details.
    *
    * @param  int  $storyId
    * @param  int  $userId
    * @return bool
    */
    public function delete(int $storyId, int $userId): bool;
	
	/**
	 * Display a user story listing.
	 *
	 * @param Illuminate\Http\Request $request
	 * @param int $userId
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function getUserStories(Request $request, int $userId): LengthAwarePaginator;

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
	 * @param int $storyId
	 * @return int $newStoryId
	 */
	public function doCopyDeclinedStory(int $storyId): int;
}
