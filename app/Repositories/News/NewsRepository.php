<?php
namespace App\Repositories\News;

use App\Repositories\News\NewsInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\News;
use Illuminate\Support\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;
use App\Models\NewsToCategory;
use App\Models\NewsLanguage;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;

class NewsRepository implements NewsInterface
{
    /**
     * @var App\Models\News
     */
    private $news;

    /**
     * @var App\Models\NewsToCategory
     */
    private $newsToCategory;

    /**
     * @var App\Models\NewsLanguage
     */
    private $newsLanguage;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * Create a new news repository instance.
     *
     * @param  App\Models\News $news
     * @param  App\Models\NewsToCategory $newsToCategory
     * @param  App\Models\NewsLanguage $newsLanguage
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        News $news,
        NewsToCategory $newsToCategory,
        NewsLanguage $newsLanguage,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->news = $news;
        $this->newsToCategory = $newsToCategory;
        $this->newsLanguage = $newsLanguage;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
    }
   
    /**
     * Display news lists.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $languageId
     * @param string $newsStatus
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNewsList(
        Request $request,
        int $languageId = null,
        string $newsStatus = null
    ): LengthAwarePaginator {
        $newsData = $this->news->select('news_id')
        ->with(['newsToCategory' => function ($query) {
            $query->with(['newsCategory' => function ($query) {
                $query->select('news_category_id', 'category_name', 'translations');
            }]);
        }]);

        if ($languageId) {
            $newsData->with(['newsLanguage' => function ($query) use ($languageId) {
                $query->select('news_id', 'language_id', 'title', 'description')->where('language_id', $languageId);
            }]);
        } else {
            $newsData->with(['newsLanguage' => function ($query) {
                $query->select('news_id', 'language_id', 'title', 'description');
            }]);
        }

        if ($newsStatus) {
            $newsData->where('status', $newsStatus);
        }
        return $newsData->paginate($request->perPage);
    }

    /**
     * Store news details.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\News
     */
    public function store(Request $request): News
    {
        // Store news details
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $newsImage = ($request->has('news_image')) ?
        $this->s3helper->uploadFileOnS3Bucket($request->news_image, $tenantName) : null;

        $newsArray = array(
            'news_image' => $newsImage,
            'user_name' => $request->user_name,
            'user_title' => $request->user_title,
            'user_thumbnail' => $request->user_thumbnail,
        );
        $news = $this->news->create($newsArray);

        // Insert into news_to_category
        $newsToCategoryArray = array(
            'news_id' => $news->news_id,
            'news_category_id' => $request->news_category_id
        );
        $this->newsToCategory->create($newsToCategoryArray);
        
        // Insert into news_language
        $languages = $this->languageHelper->getLanguages($request);
        if ($request->has('news_content')) {
            $newsContent = $request->news_content;
            foreach ($newsContent['translations'] as $value) {
                // Get language_id from language code - It will fetch data from `ci_admin` database
                $language = $languages->where('code', $value['lang'])->first();
                
                $newsLanguageData = array('news_id' => $news->news_id,
                                        'language_id' => $language->language_id,
                                        'title' => $value['title'],
                                        'description' => $value['description']);
                                        
                $this->newsLanguage->create($newsLanguageData);
            }
        }
        return $news;
    }

    /**
     * Update news.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $newsId
     * @return App\Models\News
     */
    public function update(Request $request, int $newsId): News
    {
        $newsDetails = $this->news->findOrFail($newsId);
        
        // Update news details
        if ($request->has('news_image')) {
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $newsImage = $this->s3helper->uploadFileOnS3Bucket($request->news_image, $tenantName);
            $request->request->add(['news_image' => $newsImage]);
        }
        $newsData = $newsDetails->update($request->toArray());
 
        // Update news_to_category
        if ($request->news_category_id) {
                $newsToCategoryArray = array(
                'news_category_id' => $request->news_category_id
                );
                $this->newsToCategory->where('news_id', $newsId)->update($newsToCategoryArray);
        }
        
        // Update into news_language
        $languages = $this->languageHelper->getLanguages($request);

        if ($request->has('news_content')) {
            $newsContent = $request->news_content;
            foreach ($newsContent['translations'] as $value) {
                // Get language_id from language code - It will fetch data from `ci_admin` database
                $language = $languages->where('code', $value['lang'])->first();
                
                $newsLanguageData = array('language_id' => $language->language_id,
                                        'title' => $value['title'],
                                        'description' => $value['description']
                                    );

                $this->newsLanguage->createOrUpdateNewsLanguage(['news_id' => $newsId,
                'language_id' => $language->language_id], $newsLanguageData);
            }
        }

        return $newsDetails;
    }
    
    /**
     * Get news details.
     *
     * @param int $id
     * @param int $languageId
     * @param string $newsStatus
     * @return App\Models\News
     */
    public function getNewsDetails(int $id, int $languageId = null, string $newsStatus = null): News
    {
        $newsData = $this->news
        ->with(['newsToCategory' => function ($query) {
            $query->with('newsCategory');
        }]);
    
        if ($languageId) {
            $newsData->with(['newsLanguage' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }]);
        } else {
            $newsData->with('newsLanguage');
        }

        if ($newsStatus) {
            $newsData->where('status', $newsStatus);
        }

        $newsData = $newsData->findOrFail($id);
        return $newsData;
    }

    /**
     * Remove news.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $news = $this->news->findOrFail($id);
        $newsStatus = $news->delete();
        // Delete news language data
        $news->newsLanguage()->delete();

        //Delete news_to_category data
        $news->newsToCategory()->delete();
        
        return $newsStatus;
    }
}
