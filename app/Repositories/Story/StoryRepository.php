<?php

namespace App\Repositories\Story;

use App\Models\Story;
use App\Models\StoryMedia;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use App\Repositories\Story\StoryInterface;
use Illuminate\Support\Collection;

class StoryRepository implements StoryInterface
{
    /**
     * @var App\Models\Story
     */
    private $story;

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
     * Create a new story repository instance.
     *
     * @param  App\Models\Story $story
     * @param  App\Models\StoryMedia $storyMedia
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        Story $story,
        StoryMedia $storyMedia,
        S3Helper $s3helper,
        Helpers $helpers
    ) {
        $this->story = $story;
        $this->storyMedia = $storyMedia;
        $this->s3helper = $s3helper;
        $this->helpers = $helpers;
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
        $storyData = $this->story->where(['story_id' => $storyId,
        'user_id' => $request->auth->user_id])->firstOrFail();

        $storyDataArray = $request->except(['user_id', 'published_at', 'status']);
        $storyData->update($storyDataArray);

        if($request->hasFile('story_images')) {            
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
    ): void 
    {
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
        $videosUrl = explode(",", $storyVideosUrl);
        foreach ($videosUrl as $value) {
            $storyVideo = array('story_id' => $storyId,
                'type' => 'video',
                'path' => $value);
            $this->storyMedia->create($storyVideo);
        }
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
