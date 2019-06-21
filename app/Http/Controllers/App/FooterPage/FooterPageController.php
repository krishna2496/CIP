<?php
namespace App\Http\Controllers\App\FooterPage;

use App\Repositories\FooterPage\FooterPageRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\{FooterPage, FooterPagesLanguage};
use App\Helpers\{Helpers, ResponseHelper};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class FooterPageController extends Controller
{
    /**
     * @var App\Repositories\FooterPage\FooterPageRepository
     */
    private $page;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FooterPageRepository $page, Response $response)
    {
        $this->page = $page;
        $this->response = $response;
    }
    
    /**
     * Display a listing of CMS pages.
     *
     * @return mixed
     */
    public function index()
    {
        try {
            // Get data for parent table
            $pageList = $this->page->getPageList();
            
            // No datafound
            if ($pageList->count() == 0) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return ResponseHelper::success($apiStatus, $apiMessage);
            }

            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage, $pageList->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return mixed
     */
    public function show(string $slug)
    {
        try {
            // Get data for parent table
            $footerPage = $this->page->getPageDetail($slug);
            // Check data found or not
            if ($footerPage->count() == 0) {
                throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
            }

            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage, $footerPage->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display a listing of CMS pages.
     *
     * @return mixed
     */
    public function cmsList()
    {
        try {
            // Get data for parent table
            $pageDetailList = $this->page->getPageDetailList();
            // Check data found or not
            if ($pageDetailList->count() == 0) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return ResponseHelper::success($apiStatus, $apiMessage);
            }
            
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage, $pageDetailList->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
