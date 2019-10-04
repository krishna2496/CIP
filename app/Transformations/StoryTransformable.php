<?php
namespace App\Transformations;

use App\Models\Story;

trait StoryTransformable
{
    /**
     * Select story fields
     *
     * @param App\Models\Story $story
     * @param int $defaultTenantLanguageId
     * @param int $languageId
     * @return App\Models\Story
     */
    protected function transformStory(Story $story, int $defaultTenantLanguageId, int $languageId):Story
    {
        $prop = new Story;
        $prop->story_id = (int) $story->story_id;
        $prop->mission_id = $story->mission_id;
        $prop->title = $story->title;
        $prop->description = $story->description;
        $prop->status = $story->status;
        $prop->published_at = $story->published_at;
        
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
        
        $key = array_search($languageId, array_column($story->mission->missionLanguage->toArray(), 'language_id'));
        $language = ($key === false) ? $defaultTenantLanguageId : $languageId;
        $missionLanguage = $story->mission->missionLanguage->where('language_id', $language)->first();

        if (!is_null($missionLanguage)) {
            $prop->mission_title = $missionLanguage->title;
            $prop->mission_description = $missionLanguage->short_description;
        }
        return $prop;
    }
}
