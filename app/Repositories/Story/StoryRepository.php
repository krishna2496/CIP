<?php

namespace App\Repositories\Story;

use DB;
use App\Models\Mission;
use App\Models\Story;
use App\Models\StoryMedia;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Story\StoryInterface;

class StoryRepository implements StoryInterface
{
	/**
	 * @var App\Models\Story
	 */
	private $story;
	
	/**
	 * @var App\Models\Mission
	 */
	private $mission;
	
	/**
	 * @var App\Models\StoryMedia
	 */
	private $storyMedia;
	
	/**
	 * @var App\Helpers\Helpers
	 */
	private $helpers;
	
	/**
	 * @var App\Helpers\LanguageHelper
	 */
	private $languageHelper;
	
	/**
	 * Create a new Story repository instance.
	 *
	 * @param  App\Models\Story $story
	 * @param  App\Models\Mission $mission
	 * @param  App\Models\StoryMedia $storyMedia
	 * @param  App\Helpers\Helpers $helpers
	 * @param  App\Helpers\LanguageHelper $languageHelper	
	 * @return void
	 */
	public function __construct(
			story $story,
			Mission $mission,
			StoryMedia $storyMedia,
			Helpers $helpers,
			LanguageHelper $languageHelper
			) {
				$this->story = $story;
				$this->mission = $mission;
				$this->storyMedia = $storyMedia;
				$this->helpers = $helpers;
				$this->languageHelper = $languageHelper;
	}
	
	/**
	 * Find the specified resource from database
	 *
	 * @param int $id
	 * @return App\Models\Story
	 */
	public function find(int $id): Story
	{
		return $this->story->
		with(
			'storyMedia',
			'mission',
			'user'
		)->findOrFail($id);
	}
	
	/**
	 * Remove the specified resource from database.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete(int $id): bool
	{
		return $this->story->deleteStory($id);
	}
	
	/**
	 * Display a listing of specified resources.
	 *
	 * @param Illuminate\Http\Request $request
	 * @param int $userId
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function getUserStories(Request $request,int $userId): LengthAwarePaginator
	{
		$languageId = $this->languageHelper->getLanguageId($request);
		
		$userStoryQuery = $this->story->select('story_id','mission_id','title','description','status')
		->with(['mission','storyMedia','mission.missionLanguage' => function ($query) use ($languageId) {
			$query->select('mission_language_id', 'mission_id', 'title','short_description')
			->where('language_id', $languageId);
		}])->where('user_id',$userId);
		return $userStoryQuery->paginate($request->perPage);
	}
	
	/**
	 * Display a listing of specified resources.
	 *
	 * @param int $userId
	 * @param \Illuminate\Http\Request $request
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	/*public function getUserTimesheet(int $userId, Request $request): Collection
	{
		$languageId = $this->languageHelper->getLanguageId($request);
	
		return $this->mission->select('mission.mission_id')
		->where(['publication_status' => config("constants.publication_status")["APPROVED"]])
		->whereHas('missionApplication', function ($query) use ($userId) {
			$query->where('user_id', $userId)
			->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
		})
		->with(['missionLanguage' => function ($query) use ($languageId) {
			$query->select('mission_language_id', 'mission_id', 'title')
			->where('language_id', $languageId);
		}])
		->with(['timesheet' => function ($query) use ($userId) {
			$query->where('user_id', $userId);
			$query->with('timesheetStatus');
		}])
		->get();
	}
	
	/**
	 * Get listing of Stories related to user
	 *
	 * @param Illuminate\Http\Request $request
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	/*public function userList(Request $request): LengthAwarePaginator
	{
		$tenantName = $this->helpers->getSubDomainFromRequest($request);
		$defaultAvatarImage = $this->helpers->getUserDefaultProfileImage($tenantName);
	
		$userQuery = $this->user->selectRaw("first_name, last_name, email, password,
				case when(avatar = '' || avatar is null) then '$defaultAvatarImage' else avatar end as avatar,
				timezone_id, availability_id, why_i_volunteer, employee_id, department,
				manager_name, city_id, country_id, profile_text, linked_in_url, status, language_id, title")
				->with('city', 'country', 'timezone');
	
				if ($request->has('search')) {
					$userQuery->where(function ($query) use ($request) {
						$query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
						$query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
					});
				}
				if ($request->has('order')) {
					$orderDirection = $request->input('order', 'asc');
					$userQuery->orderBy('user_id', $orderDirection);
				}
	
				return $userQuery->paginate($request->perPage);
	}*/
		
}