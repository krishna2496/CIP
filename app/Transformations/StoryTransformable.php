<?php
namespace App\Transformations;

use App\Models\Story;
use Carbon\Carbon;

trait StoryTransformable
{
    /**
     * Get Transfered stories
     *
     * @param App\Models\Story $story
     * @return App\Models\Story
     */
    protected function transformStory(Story $story):Story
    {
        $prop = new Story;
        $prop->story_id = (int) $story->story_id;
        $prop->mission_id = $story->mission_id;
        $prop->title = $story->title;
        $prop->description = $story->description;
        $prop->status = trans('messages.status.'.$story->status);
        
        if (!empty($story->user)) {
        	$prop->user_id = $story->user_id;
        	$prop->first_name = $story->user->first_name;
        	$prop->last_name = $story->user->last_name;
        	$prop->avatar = $story->user->avatar;
        	$prop->profile_text = $story->user->profile_text;
        	$prop->why_i_volunteer = $story->user->why_i_volunteer;
        	$prop->city = $story->user->city;
        	$prop->country = $story->user->country;
        }
        
        if (!empty($story->storyMedia)) {
        	$prop->storyMedia = $story->storyMedia;
        }
        
        if ($story->mission->missionLanguage->isNotEmpty()) {
        	$prop->mission_title = $story->mission->missionLanguage[0]->title;
        	$prop->mission_description = $story->mission->missionLanguage[0]->short_description;
        }       
        return $prop;
    }
    
    /**
     * Used for transform user stories 
     * 
     * @param Object $story
     * @return array
     */
    protected function transformUserRelatedStory(Object $story): array
    {	
    	$userStories = $story->toArray();
    	$transformedUserStories = array();
    	
    	$draftStory = $publishedStory = $pendingStories = $declinedStories = 0;
    	foreach($story as $storyData)
    	{
    		switch ($storyData->status){
    			case "DRAFT":
    				$draftStory++;
    				break;
    			case "PENDING":
    				$pendingStories++;
    				break;
    			case "PUBLISHED":
    				$publishedStory++;
    				break;
    			case "DECLINED":
    				$declinedStories++;
    				break;
    		}
    		
    		$transformedUserStories [] = [
    			'story_id' => (int) $storyData->story_id,
    			'mission_id' => $storyData->mission_id,
    			'title' => $storyData->title,
    			'description' => $storyData->description,
    			'status' => trans('messages.status.'.$storyData->status),
    			'storyMedia' => $storyData->storyMedia->first(),
    			'created' =>  Carbon::parse($storyData->created_at)->format('d/m/Y'),
    		];
    	}
    	
    	$transformedUserStories ['draft_story_count'] = $draftStory;
    	$transformedUserStories ['published_story_count'] = $publishedStory;
    	$transformedUserStories ['pending_story_count'] = $pendingStories;
    	$transformedUserStories ['declined_story_count'] = $declinedStories;
    	
    	return $transformedUserStories;
    }
    
    /**
     * Used for transform published stories
     *
     * @param Object $story
     * @return array
     */
    protected function transformPublishedStory(Object $story): array
    {
    	$transformedPublishedStories = array();
    	
    	$languageCode = config('app.locale');
    	foreach($story as $storyData)
    	{
    		// get the theme name based on language set
    		$themeName = $storyData->mission->missionTheme->theme_name;
    		
    		$arrayKey = array_search($languageCode, array_column(
    			$storyData->mission->missionTheme['translations'],
    			'lang'
    		));
    		
    		if ($arrayKey  !== false) {
    			$themeName = $storyData->mission->missionTheme['translations'][$arrayKey]['title'];
    		}
    		
    		$transformedPublishedStories [] = [
    				'story_id' => (int) $storyData->story_id,
    				'mission_id' => $storyData->mission_id,
    				'user_id' => $storyData->user_id,
    				'user_first_name' => $storyData->user->first_name,
    				'user_last_name' => $storyData->user->last_name,
    				'user_avatar' => $storyData->user->avatar,
    				'title' => $storyData->title,
    				'description' => $storyData->description,
    				'status' => trans('messages.status.'.$storyData->status),
    				'storyMedia' => $storyData->storyMedia->first(),
    				'published_at' =>  Carbon::parse($storyData->published_at)->format('d/m/Y'),
    				'theme_name' => $themeName
    		];
    	} 
    	
    	return $transformedPublishedStories;
    }
}
