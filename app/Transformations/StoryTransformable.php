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
        
        if ($story->mission->missionLanguage->isNotEmpty()) {
        	$prop->mission_title = $story->mission->missionLanguage[0]->title;
        	$prop->mission_description = $story->mission->missionLanguage[0]->short_description;
        }       
        return $prop;
    }
}
