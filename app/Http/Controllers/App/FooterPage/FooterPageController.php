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
use App\Traits\RestExceptionHandlerTrait;
use Validator;

class FooterPageController extends Controller
{
    use RestExceptionHandlerTrait;
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
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get data for parent table
            $pageList = $this->footerPageRepository->getPageList();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            $apiMessage = ($pageList->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $pageList->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        try {
            // Get data for parent table
            $footerPage = $this->footerPageRepository->getPageDetail($slug);
            // Check data found or not
            if ($footerPage->count() == 0) {
                return $this->modelNotFound(
                    config('constants.error_codes.ERROR_NO_DATA_FOUND'),
                    trans('messages.custom_error_message.ERROR_NO_DATA_FOUND')
                );
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $footerPage->toArray());
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_NO_DATA_FOUND'),
                trans('messages.custom_error_message.ERROR_NO_DATA_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display a listing of CMS pages.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function cmsList(): JsonResponse
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
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $pageDetailList->toArray());
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
