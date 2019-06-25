<?php
namespace App\Http\Controllers\App\FooterPage;

use App\Repositories\FooterPage\FooterPageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\FooterPage;
use App\Models\FooterPagesLanguage;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class FooterPageController extends Controller
{
    /**
     * @var App\Repositories\FooterPage\FooterPageRepository
     */
    private $footerPageRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\FooterPage\FooterPageRepository $footerPageRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(FooterPageRepository $footerPageRepository, ResponseHelper $responseHelper)
    {
        $this->footerPageRepository = $footerPageRepository;
        $this->responseHelper = $responseHelper;
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
            $pageList = $this->footerPageRepository->getPageList();
            
            // No datafound
            if ($pageList->count() == 0) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return $this->responseHelper->success($apiStatus, $apiMessage);
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage, $pageList->toArray());
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
            $footerPage = $this->footerPageRepository->getPageDetail($slug);
            // Check data found or not
            if ($footerPage->count() == 0) {
                throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
            }

            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage, $footerPage->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        } catch (\Exception $e) {
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
            $pageDetailList = $this->footerPageRepository->getPageDetailList();
            // Check data found or not
            if ($pageDetailList->count() == 0) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return $this->responseHelper->success($apiStatus, $apiMessage);
            }
            
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage, $pageDetailList->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
