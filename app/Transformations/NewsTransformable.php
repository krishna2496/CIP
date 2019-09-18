<?php
namespace App\Transformations;

use App\Models\News;

trait NewsTransformable
{
    /**
     * Select news listing
     *
     * @param App\Models\News $news
     * @return array
     */
    protected function transformNews(News $news): array
    {
        return $this->getTransformedNews($news, true);
    }

    /**
     * Transform news details
     *
     * @param App\Models\News $news
     * @return array
     */
    protected function transformNewsDetails(News $news): array
    {
        return $this->getTransformedNews($news);
    }
    
    /**
     * Get transformed news
     *
     * @param App\Models\News $news
     * @param bool $sortDescription 
     * @return array
     */
    protected function getTransformedNews(News $news,bool $sortDescription = null): array
    {
        $newsLanguage['title'] = array();
        $newsLanguage['description'] = array();
        $newsData = array();
        $newsCategory = null;
        $newsDetails = $news->toArray();

        if (isset($newsDetails['news_id'])) {
            $newsId = $newsDetails['news_id'];
        }

        if (isset($newsDetails['news_language'])) {
            foreach ($newsDetails['news_language'] as $key => $value) {
                $newsDetails[$key] = ['title' => $value['title']];
                $newsLanguage['title'][$key] = $newsDetails[$key];
                
                // News language description
                $description = $value['description'];
                if ($sortDescription) {
                    $sortDescription = substr($description, 0, config('constants.NEWS_DESCRIPTION_CHARACTER_LIMIT'));
                    $newsDetails[$key] = ['description' => $sortDescription.'...'];
                    $newsLanguage['description'][$key] = $newsDetails[$key];
                } else {
                    $newsDetails[$key] = ['description' => $description];
                    $newsLanguage['description'][$key] = $newsDetails[$key];
                }
            }
        } 
        
        if (isset($newsDetails['news_to_category'][0]['news_category'][0]['translations'])) {            
            $languageCode = config('app.locale');
            foreach ($newsDetails['news_to_category'][0]['news_category'] as $news) {
                $arrayKey = array_search($languageCode, array_column(
                    $news['translations'],
                    'lang'
                ));
                if ($arrayKey  !== false) {
                    $newsCategory = $news['translations'][$arrayKey]['title'];
                }
                unset($news['translations']);
            }
        }

        if (isset($newsDetails['news_image'])) {
            $newsData['news_image'] = $newsDetails['news_image'];
        }

        if (isset($newsDetails['user_title'])) {
            $newsData['user_title'] = $newsDetails['user_title'];
        }

        if (isset($newsDetails['user_name'])) {
            $newsData['user_name'] = $newsDetails['user_name'];
        }

        if (isset($newsDetails['user_thumbnail'])) {
            $newsData['user_thumbnail'] = $newsDetails['user_thumbnail'];
        }
        
        unset($newsDetails);
        $newsDetails['news_id'] = $newsId;
        $newsDetails = isset($newsData) ? array_merge($newsDetails, $newsData) : $newsDetails;
        $newsDetails['news_category'] = $newsCategory;
        $newsDetails['news_language'] = array_merge($newsLanguage['title'], $newsLanguage['description']);
        return $newsDetails;
    }
}
