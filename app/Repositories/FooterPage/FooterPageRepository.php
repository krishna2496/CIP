<?php
namespace App\Repositories\FooterPage;

use App\Repositories\FooterPage\FooterPageInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FooterPage;
use App\Models\FooterPagesLanguage;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use DB;
use Illuminate\Support\Collection;

class FooterPageRepository implements FooterPageInterface
{
    /**
     * @var App\Models\FooterPage
     */
    private $page;
    
    /**
     * @var App\Models\FooterPagesLanguage
     */
    private $footerPageLanguage;
    
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(FooterPage $page, FooterPagesLanguage $footerPageLanguage)
    {
        $this->page = $page;
        $this->footerPageLanguage = $footerPageLanguage;
    }
    
    /**
     * Store a newly created resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\FooterPage
     */
    public function store(Request $request): FooterPage
    {
        $postData = $request->page_details;
        // Set data for create new record
        $page = array();
        $page['status'] = config('constants.ACTIVE');
        $page['slug'] = $postData['slug'];
        // Create new cms page
        $footerPage = $this->page->create($page);
        
        $languages = LanguageHelper::getLanguages($request);
        
        foreach ($postData['translations'] as $value) {
            // Get language_id from language code - It will fetch data from `ci_admin` database
            $language = $languages->where('code', $value['lang'])->first();
            
            $footerPageLanguageData = array('page_id' => $footerPage['page_id'],
                                      'language_id' => $language->language_id,
                                      'title' => $value['title'],
                                      'description' => $value['sections']);
                                      
            $this->footerPageLanguage->create($footerPageLanguageData);
            
            unset($footerPageLanguageData);
        }
        return $footerPage;
    }
    
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return App\Models\FooterPage
    */
    public function update(Request $request, int $id): FooterPage
    {
        $postData = $request->page_details;
        
        // Set data for update record
        $page = array();
        if (isset($postData['status'])) {
            $page['status'] = $postData['status'];
        }
        if (isset($postData['slug'])) {
            $page['slug'] = $postData['slug'];
        }
        
        // Update footer page
        $footerPage = $this->page->findOrFail($id);
        $footerPage->update($page);
        
        $languages = LanguageHelper::getLanguages($request);
                 
        if (isset($postData['translations'])) {
            foreach ($postData['translations'] as $value) {
                $language = $languages->where('code', $value['lang'])->first();
                
                $footerPageData = $this->footerPageLanguage->where('page_id', $id)
                                ->where('language_id', $language->language_id)
                                ->count();
                
                $pageLanguageData = ['title' => $value['title'], 'description' => serialize($value['sections'])];
            
                // If record exist then update it otherwise create new record
                if ($footerPageData > 0) {
                    $footerPageLanguage = $this->footerPageLanguage->where('page_id', $id)
                                        ->where('language_id', $language->language_id)
                                        ->update($pageLanguageData);
                } else {
                    $pageLanguageData['page_id'] = $footerPage['page_id'];
                    $pageLanguageData['language_id'] = $language->language_id;
                    $footerPageLanguage = FooterPagesLanguage::create($pageLanguageData);
                }
                    
                unset($pageLanguageData);
            }
        }
        return $footerPage;
    }
    
    /**
    * Display a listing of footer pages.
    *
    * @param Illuminate\Http\Request $request
    * @return App\Models\FooterPage
    */
    public function footerPageList(Request $request): FooterPage
    {
        $pageQuery = $this->page->with('pageTranslations');
        
        if ($request->has('search')) {
            $pageQuery->wherehas('pageTranslations', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->input('search') . '%');
                $q->orWhere('description', 'like', '%' . $request->input('search') . '%');
            });
        }
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $pageQuery->orderBy('page_id', $orderDirection);
        }

        return $pageQuery->paginate(config('constants.PER_PAGE_LIMIT'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->page->deleteFooterPage($id);
    }

    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function getPageList(): Collection
    {
        return $this->page->with(['pages:page_id,language_id,title'])->get();
    }

    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function getPageDetailList(): Collection
    {
        return $this->page->with(['pages:page_id,language_id,title,description as sections'])->get();
    }

    /**
    * Get a listing of resource.
    *
    * @return App\Models\FooterPage
    */
    public function getPageDetail($slug): FooterPage
    {
        return $this->page->with(['pages:page_id,language_id,title,description as sections'])
        ->whereSlug($slug)->first();
    }
}
