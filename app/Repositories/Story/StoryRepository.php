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
use Carbon\Carbon;
use App\Helpers\S3Helper;
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
     * @var App\Helpers\S3Helper
     */
    private $s3helper;
    
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
        );

        $storyData = $this->story->create($storyDataArray);
       
        // Store story image
        if ($request->hasFile('story_images')) {
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $files = $request->file('story_images');
            foreach ($files as $file) {
                $filePath = $this->s3helper
                ->uploadDocumentOnS3Bucket(
                    $file,
                    $tenantName,
                    $request->auth->user_id,
                    config('constants.folder_name.story')
                );
                $storyImage = array('story_id' => $storyData->story_id,
                                        'type' => 'image',
                                        'path' => $filePath);
                $this->storyMedia->create($storyImage);
            }
        }

        // Store story video url
        if ($request->has('story_videos')) {
            foreach ($request->story_videos as $value) {
                $storyVideo = array('story_id' => $storyData->story_id,
                    'type' => 'video',
                    'path' => $value);
                $this->storyMedia->create($storyVideo);
            }
        }

        return $storyData;
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
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserStoriesWithPagination(Request $request, int $userId): LengthAwarePaginator
    {
        $language = $this->languageHelper->getLanguageDetails($request);
        
        $userStoryQuery = $this->story->select(
            'story_id',
            'mission_id',
            'title',
            'description',
            'status',
            'published_at'
        )
        ->with(['mission','storyMedia','mission.missionLanguage' => function ($query) use ($language) {
            $query->select('mission_language_id', 'mission_id', 'language_id', 'title', 'short_description')
            ->where('language_id', $language->language_id);
        }])->where('user_id', $userId);
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
        $updateData = ['status' => $storyStatus,'published_at'=> null];
        
        if ($storyStatus=='PUBLISHED') {
            $updateData ['published_at'] = Carbon::now()->toDateTimeString();
            ;
        }
        return $this->story->where('story_id', $storyId)
        ->update($updateData);
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
        $storyQuery = $this->story
        ->with(['user','user.city','user.country','storyMedia']);
        
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
}
