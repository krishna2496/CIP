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
     * Get transformed news
     *
     * @param App\Models\News $news
     * @param bool $sortDescription 
     * @return array
     */
    protected function getTransformedNews(News $news, bool $sortDescription = null): array
    {
		$newsDetails = $news->toArray();
		
		$transformedNews = array();
		$transformedNews['news_id'] = $newsDetails['news_id'];
		$transformedNews['news_image'] = $newsDetails['news_image'];
		$transformedNews['user_name'] = $newsDetails['user_name'];
		$transformedNews['user_title'] = $newsDetails['user_title'];
		$transformedNews['user_thumbnail'] = $newsDetails['user_thumbnail'];
		$transformedNews['published_on'] = $newsDetails['created_at'];
		
		if (isset($newsDetails['news_language']) && !empty($newsDetails['news_language'])) {
            if (count($newsDetails['news_language']) > 1) {
                foreach ($newsDetails['news_language'] as $key => $value) {
                    $newsContent[$key]['language_id'] = $value['language_id'];
                    $newsContent[$key]['title'] = $value['title'];
                    $newsContent[$key]['description'] = ($sortDescription) ? $this->helpers->trimText(
                            strip_tags($value['description']),
                            config('constants.NEWS_SHORT_DESCRIPTION_WORD_LIMIT')
                        ) : $value['description'];
                }                
            } else {
                $description = $newsDetails['news_language'][0]['description'];
                $newsContent['language_id'] = $newsDetails['news_language'][0]['language_id'];
                $newsContent['title'] = $newsDetails['news_language'][0]['title'];
                $newsContent['description'] = ($sortDescription) ? $this->helpers->trimText(
                            strip_tags($description),
                            config('constants.NEWS_SHORT_DESCRIPTION_WORD_LIMIT')
                        ) : $description;
            }  	
			$transformedNews['news_content'] = $newsContent;
        }
		
		if (isset($newsDetails['news_to_category'])) {
			$newsCategoryArray = array();
			foreach ($newsDetails['news_to_category'] as $key => $value) {
				$newsCategoryArray[$key]['news_category_id'] = $value['news_category_id'];
				$languageCode = config('app.locale');
				foreach ($newsDetails['news_to_category'][$key]['news_category'] as $category) {
					$arrayIndex = array_search($languageCode, array_column(
						$category['translations'],
						'lang'
					));
					if ($arrayIndex  !== false) {
						$newsCategory[] = $category['translations'][$arrayIndex]['title'];
					}
					unset($category['translations']);
				}
			}
			$transformedNews['news_category'] = $newsCategory;
	    }
		return $transformedNews;
	}
}
