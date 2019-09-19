<?php
namespace App\Transformations;

use App\Models\News;
use App\Helpers\Helpers;

trait NewsTransformable
{
    private $helpers;

    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

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
    protected function getTransformedNews(News $news, bool $sortDescription = null): array
    {
        $newsContent = array();
        $newsLanguage['title'] = array();
        $newsData = array();
        $newsCategory = null;
        $newsData['user_thumbnail'] = null;
        $newsData['news_image'] = null;
        $newsData['user_title'] = null;
        $newsData['user_name'] = null;
        $description = null;
        $newsDetails = $news->toArray();

        if (isset($newsDetails['news_id'])) {
            $newsId = $newsDetails['news_id'];
        }

        if (isset($newsDetails['news_language']) && !empty($newsDetails['news_language'])) {
            if (count($newsDetails['news_language']) > 1) {
                foreach ($newsDetails['news_language'] as $key => $value) {
                    $description = $value['description'];
                    if ($sortDescription) {
                        $sortDescription = isset($description) ? 
                        $this->helpers->shortDescription(
                            $description,
                            config('constants.NEWS_SHORT_DESCRIPTION_WORD_LIMIT')
                        ) : null;
                        $description = $sortDescription;
                    }
                    $newsContent['language_id'] = $value['language_id'];
                    $newsContent['title'] = $value['title'];
                    $newsContent['description'] = $description;
                    $newsContentDetails[$key] = $newsContent;      
                }                
            } else {
                $newsTitle = $newsDetails['news_language'][0]['title'];
                $description = $newsDetails['news_language'][0]['description'];
                if ($sortDescription) {
                    $sortDescription = isset($description) ? 
                    $this->helpers->shortDescription(
                        $description,
                        config('constants.NEWS_SHORT_DESCRIPTION_WORD_LIMIT')
                    ) : null;
                    
                    $newsDescription = $sortDescription;
                } else {
                    $newsDescription = $description;
                }
                
                $newsContent['language_id'] = $newsDetails['news_language'][0]['language_id'];
                $newsContent['title'] = $newsTitle;
                $newsContent['description'] = $newsDescription;
                $newsContentDetails = $newsContent;
            }            
        } else {
            $newsContent['language_id'] = null;
            $newsContent['title'] = null;
            $newsContent['description'] = null;
            $newsContentDetails = $newsContent;
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

        
        if (isset($newsData['user_thumbnail'])
            || isset($newsData['news_image'])
            || isset($newsData['user_title'])
            || isset($newsData['user_name'])
        ) {
           $newsDataArray = $newsData;
        } else {
            unset($newsData);
        }

        unset($newsDetails);
        $newsDetails['news_id'] = $newsId;
        $newsDetails = isset($newsDataArray) ? array_merge($newsDetails, $newsDataArray) : $newsDetails;
        $newsDetails['news_category'] = $newsCategory;
        $newsDetails['news_content'] = $newsContentDetails;
        return $newsDetails;
    }
}
