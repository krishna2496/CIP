<?php
namespace App\Transformations;

use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait StoryTransformable
{
    /**
     * Get Transfomed stories
     *
     * @param App\Models\Story $story
     * @param int $languageId
     * @return App\Models\Story
     */

    protected function transformStory(Story $story, int $languageId):Story
    {
        $storyData = new Story;
        $storyData->story_id = (int) $story->story_id;
        $storyData->mission_id = $story->mission_id;
        $storyData->title = $story->title;
        $storyData->description = $story->description;
        $storyData->story_visitor_count = (int) $story->story_visitor_count;
        $storyData->status = trans('general.status.' . $story->status);
        $storyData->published_at = $story->published_at;

        if (!empty($storyData->user)) {
            $storyData->user_id = $story->user_id;
            $storyData->first_name = $story->user->first_name;
            $storyData->last_name = $story->user->last_name;
            $storyData->avatar = $story->user->avatar;
            $storyData->profile_text = $story->user->profile_text;
            $storyData->why_i_volunteer = $story->user->why_i_volunteer;
            $storyData->city = $story->user->city;
            $storyData->country = $story->user->country;
        }

        if (!empty($storyData->storyMedia)) {
            $storyData->storyMedia = $story->storyMedia;
        }

        
        $key = array_search($languageId, array_column($storyData->mission->missionLanguage->toArray(), 'language_id'));
        $language = ($key === false) ? 'en' : $languageId;
        $missionLanguage = $storyData->mission->missionLanguage->where('language_id', $language)->first();
        
        if (!is_null($missionLanguage)) {
            $storyData->mission_title = $missionLanguage->title;
        }

        return $storyData;
    }

    /**
     * Used for transform user stories
     *
     * @param Object $stories
     * @return array
     */
    protected function transformUserStories(Object $stories): array
    {
        $transformedUserStories = array();

        $draftStories = $publishedStories = $pendingStories = $declinedStories = 0;
        foreach ($stories as $story) {
            switch ($story->status) {
                case "DRAFT":
                    $draftStories++;
                    break;
                case "PENDING":
                    $pendingStories++;
                    break;
                case "PUBLISHED":
                    $publishedStories++;
                    break;
                case "DECLINED":
                    $declinedStories++;
                    break;
            }

            $transformedUserStories['story_data'][] = [
                'story_id' => (int) $story->story_id,
                'mission_id' => $story->mission_id,
                'title' => $story->title,
                'description' => $story->description,
                'status' => trans('general.status.' . $story->status),
                'storyMedia' => $story->storyMedia->first(),
                'created' => Carbon::parse($story->created_at)->format('d/m/Y'),
            ];
        }
        if (count($stories) > 0) {
            $transformedUserStories['stats']['draft'] = $draftStories;
            $transformedUserStories['stats']['published'] = $publishedStories;
            $transformedUserStories['stats']['pending'] = $pendingStories;
            $transformedUserStories['stats']['declined'] = $declinedStories;
        }
        
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
        foreach ($story as $storyData) {
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
                    'status' => trans('general.status.'.$storyData->status),
                    'storyMedia' => $storyData->storyMedia->first(),
                    'published_at' =>  Carbon::parse($storyData->published_at)->format('d/m/Y'),
                    'theme_name' => $themeName
            ];
        }

        return $transformedPublishedStories;
    }

    /**
     * Get Transfomed story detail
     *
     * @param App\Models\Story $story
     * @param int $storyViewCount
     * @return Array
     */

    protected function transformStoryDetail(Story $story, int $storyViewCount):array
    {
        $storyData['story_id'] = (int) $story->story_id;
        $storyData['mission_id'] = $story->mission_id;
        $storyData['title'] = $story->title;
        $storyData['description'] = $story->description;
        $storyData['story_visitor_count'] = $storyViewCount;
        $storyData['status'] = trans('general.status.' . $story->status);
        $storyData['published_at'] = $story->published_at;

        if (!empty($story->user)) {
            $storyData['user_id'] = $story->user_id;
            $storyData['first_name'] = $story->user->first_name;
            $storyData['last_name'] = $story->user->last_name;
            $storyData['avatar'] = $story->user->avatar;
            $storyData['profile_text'] = $story->user->profile_text;
            $storyData['why_i_volunteer'] = $story->user->why_i_volunteer;
            $storyData['city'] = $story->user->city;
            $storyData['country'] = $story->user->country;
        }

        if (!empty($story->storyMedia)) {
            $storyData['storyMedia'] = $story->storyMedia;
        }
        return $storyData;
    }
}
