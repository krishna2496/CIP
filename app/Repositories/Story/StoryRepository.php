<?php

namespace App\Repositories\Story;

use App\Models\Story;
use App\Models\StoryMedia;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use App\Repositories\Story\StoryInterface;

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
}
