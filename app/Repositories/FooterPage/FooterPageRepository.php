<?php

namespace App\Repositories\FooterPage;

use App\Repositories\FooterPage\FooterPageInterface;
use Illuminate\Http\{Request, Response};
use DB;
use App\Models\{FooterPage, FooterPagesLanguage};
use App\Helpers\{Helpers, LanguageHelper};

class FooterPageRepository implements FooterPageInterface
{
    private $page;
	
	private $footerPageLanguage;

    function __construct(FooterPage $page, FooterPagesLanguage $footerPageLanguage) {
		$this->page = $page;
		$this->footerPageLanguage = $footerPageLanguage;
    }
	
	public function store(Request $request)
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
	
	public function update(Request $request, int $id) {
		
		$postData = $request->page_details;
		
		// Set data for update record
		$page = array();
		$page['status'] = $postData['status'];
		$page['slug'] = $postData['slug'];
		
		// Create new cms page
		$footerPage = $this->page->findOrFail($id);
		$footerPage->update($page);
		
		$languages = LanguageHelper::getLanguages($request);
    	         
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
			} else{
				$pageLanguageData['page_id'] = $footerPage['page_id'];
				$pageLanguageData['language_id'] = $language->language_id;
				$footerPageLanguage = FooterPagesLanguage::create($pageLanguageData);
			}
				
			unset($pageLanguageData);                    
		}      
		return $footerPage;       
	}
	
	public function footerPageList(Request $request) {
		
		try {
            $pageQuery = $this->page->with('pageTranslations');
			
            if ($request->has('search')) {
                $pageQuery->wherehas('pageTranslations', function($q) use($request) {
						$q->where('title', 'like', '%' . $request->input('search') . '%');
						$q->orWhere('description', 'like', '%' . $request->input('search') . '%');
				});
            }
            if ($request->has('order')) {
                $orderDirection = $request->input('order', 'asc');
                $pageQuery->orderBy('page_id', $orderDirection);
            }

            return $pageQuery->paginate(config('constants.PER_PAGE_LIMIT'));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
		
	}
	
	public function userList(Request $request) {
		
		try {
			
			$userQuery = $this->user->with('city', 'country', 'timezone');
			
			if ($request->has('search')) {
				$userQuery->where(function($query) use($request) {
					$query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
					$query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
				});
			}
			if ($request->has('order')) {
				$orderDirection = $request->input('order','asc');
				$userQuery->orderBy('user_id', $orderDirection);
			}
			
			$userList = $userQuery->paginate(config('constants.PER_PAGE_LIMIT'));
			$responseMessage = (count($userList) > 0) ? trans('messages.success.MESSAGE_USER_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
			
			return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $userList);
			
		} catch(\InvalidArgumentException $e) {
			
			throw new \InvalidArgumentException($e->getMessage());
			
		}
	}

    public function find(int $id) {
		
		try {         
            
			$userDetail = $this->user->findUser($id);
			
			$apiData = $userDetail->toArray();
			$apiStatus = $this->response->status();
			$apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
			
			return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
			
		} catch(ModelNotFoundException $e){
			
			throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
			
        } catch(\Exception $e) {
			
			throw new \Exception($e->getMessage());
			
		}	
	}
	
    public function delete(int $id) {
		return $this->page->deleteFooterPage($id);
	}
}