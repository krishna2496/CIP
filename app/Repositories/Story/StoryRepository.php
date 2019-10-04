<?php

namespace App\Repositories\Story;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\Mission;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Repositories\Story\StoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StoryRepository implements StoryInterface
{
    /**
     *
     * @var App\Models\Story
     */
    private $story;

    /**
     *
     * @var App\Models\Mission
     */
    private $mission;

    /**
     *
     * @var App\Models\StoryMedia
     */
    private $storyMedia;

    /**
     *
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     *
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     *
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
        S3Helper $s3helper,
        Helpers $helpers,
        LanguageHelper $languageHelper
    ) {
        $this->story = $story;
        $this->mission = $mission;
        $this->storyMedia = $storyMedia;
        $this->s3helper = $s3helper;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
    }
    

    /**
     * Store story details
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Story
     */
    public function store(Request $request): Story
    {
        $storyDataArray = array(
            'mission_id' => $request->mission_id,
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->auth->user_id,
            'status' => config('constants.story_status.DRAFT')
        );

        $storyData = $this->story->create($storyDataArray);
      
        if ($request->hasFile('story_images')) {
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            // Store story images
            $this->storeStoryImages(
                $tenantName,
                $storyData->story_id,
                $request->file('story_images'),
                $request->auth->user_id
            );
        }

        if ($request->has('story_videos')) {
            // Store story video url
            $this->storeStoryVideoUrl($request->story_videos, $storyData->story_id);
        }
        return $storyData;
    }

    /**
     * Update story details
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @return App\Models\Story
     */
    public function update(Request $request, int $storyId): Story
    {
        // Find story
        $story = $this->story->where(['story_id' => $storyId,
        'user_id' => $request->auth->user_id])->firstOrFail();

        $storyDataArray = $request->except(['user_id', 'published_at', 'status']);
        $storyDataArray['status'] = config('constants.story_status.DRAFT');
        $story->update($storyDataArray);

        if ($request->hasFile('story_images')) {
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            // Store story images
            $this->storeStoryImages(
                $tenantName,
                $story->story_id,
                $request->file('story_images'),
                $request->auth->user_id
            );
        }

        if ($request->has('story_videos')) {
            // Store story video url
            $this->storeStoryVideoUrl($request->story_videos, $story->story_id);
        }

        return $story;
    }

    /**
     * Remove the story details.
     *
     * @param  int  $storyId
     * @param  int  $userId
     * @return bool
     */
    public function delete(int $storyId, int $userId): bool
    {
        return $this->story->deleteStory($storyId, $userId);
    }
    
    /**
     * Display a listing of specified resources with pagination.
     *
     * @param Illuminate\Http\Request $request
     * @param int $userId
     * @param string $status
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserStoriesWithPagination(Request $request, int $userId = null, string $status = null): LengthAwarePaginator
    {
        $languageId = $this->languageHelper->getLanguageId($request);

        $userStoryQuery = $this->story->select('story_id', 'user_id', 'mission_id', 'title', 'description', 'status', 'published_at', 'created_at')->with([
            'user',
            'mission',
            'mission.missionTheme',
            'storyMedia',
            'mission.missionLanguage' => function ($query) use ($languageId) {
                $query->select('mission_language_id', 'mission_id', 'title', 'short_description')->where('language_id', $languageId);
            },
        ])->when($userId, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })->when($status, function ($query, $status) {
            return $query->where('status', $status);
        });
        return $userStoryQuery->paginate($request->perPage);
    }

    /**
     * Update story status field value, based on storyId condition
     *
     * @param string $storyStatus
     * @param int $storyId
     * @return bool
     */
    public function updateStoryStatus(string $storyStatus, int $storyId): bool
    {
        // default story array to update
        $updateData = [
            'status' => $storyStatus,
            'published_at' => null,
        ];

        if ($storyStatus == 'PUBLISHED') {
            $updateData['published_at'] = Carbon::now()->toDateTimeString();
        }
        return $this->story->where('story_id', $storyId)->update($updateData);
    }

    /**
     * Get story details.
     *
     * @param int $storyId
     * @param string $storyStatus
     * @return App\Models\Story
     */
    public function getStoryDetails(int $storyId, string $storyStatus = null): Story
    {
        $storyQuery = $this->story->with([
            'user',
            'user.city',
            'user.country',
            'storyMedia',
        ]);

        if (!empty($storyStatus)) {
            $storyQuery->where('status', $storyStatus);
        }

        return $storyQuery->findOrFail($storyId);
    }

    /**
     * Do copy of declined story data
     *
     * @param int $storyId
     * @return int $newStoryId
     */
    public function doCopyDeclinedStory(int $storyId): int
    {
        $newStory = $this->story->with(['storyMedia'])->findOrFail($storyId)->replicate();

        $newStory->title = 'Copy of '.$newStory->title;
        $newStory->status = config('constants.story_status.DRAFT');
        $newStory->save();
        
        $newStoryId = $newStory->story_id;

        foreach ($newStory->storyMedia as $val) {
            $this->storyMedia = new StoryMedia();
            $this->storyMedia->story_id = $newStoryId;
            $this->storyMedia->type = $val->type;
            $this->storyMedia->path = $val->path;
            $this->storyMedia->save();
        }
        return $newStoryId;
    }

    /**
     * Display a listing of specified resources without pagination.
     *
     * @param Illuminate\Http\Request $request
     * @param int $userId
     * @return Object
     */
    public function getUserStoriesWithOutPagination(Request $request, int $userId): Object
    {

        $language = $this->languageHelper->getLanguageDetails($request);
    
        $userStoryQuery = $this->story->select('story_id', 'mission_id', 'title', 'description', 'status')
        ->with(['mission','storyMedia','mission.missionLanguage' => function ($query) use ($language) {
            $query->select('mission_language_id', 'mission_id', 'title', 'short_description')
            ->where('language_id', $language->language_id);
        }])->where('user_id', $userId);
        return $userStoryQuery->get();
    }

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
    ): void {
        foreach ($storyImages as $file) {
            $filePath = $this->s3helper
            ->uploadDocumentOnS3Bucket(
                $file,
                $tenantName,
                $userId,
                config('constants.folder_name.story')
            );
            $storyImage = array('story_id' => $storyId,
                                    'type' => 'image',
                                    'path' => $filePath);
            $this->storyMedia->create($storyImage);
        }
    }

    /**
     * Store story videos url.
     *
     * @param string $storyVideosUrl
     * @param int $storyId
     * @return void
     */
    public function storeStoryVideoUrl(string $storyVideosUrl, int $storyId): void
    {
        $storyVideo = array('story_id' => $storyId,
        'type' => 'video',
        'path' => $storyVideosUrl);
        $this->storyMedia->updateOrCreate(['story_id' => $storyId, 'type' => 'video'], ['path' => $storyVideosUrl]);
    }

    /**
     * Check story status
     *
     * @param int $userId
     * @param int $storyId
     * @param array $storyStatus
     *
     * @return bool
     */
    public function checkStoryStatus(int $userId, int $storyId, array $storyStatus): bool
    {
        $storyDetails = $this->story
        ->where(['user_id' => $userId, 'story_id' => $storyId])
        ->whereIn('status', $storyStatus)
        ->get();
        $storyStatus = ($storyDetails->count() > 0) ? false : true;
        return $storyStatus;
    }
}
