<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use PDOException;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class CityController extends Controller
{
    /**
     * @var CityRepository
     */
    private $cityRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
        
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(CityRepository $cityRepository, ResponseHelper $responseHelper)
    {
        $this->cityRepository = $cityRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $city = $this->cityRepository->cityList($request);
            $cityData = $city->toArray();
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (empty($cityData)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_CITY_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $cityData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
