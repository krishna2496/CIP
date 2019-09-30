<?php
namespace App\Transformations;

use App\Models\Story;

trait StoryTransformable
{
    /**
     * Select story fields
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
        $prop->status = $story->status;
        
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
}
