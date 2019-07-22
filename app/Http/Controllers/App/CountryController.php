<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\Country\CountryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use PDOException;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class CountryController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(CountryRepository $countryRepository, ResponseHelper $responseHelper)
    {
        $this->countryRepository = $countryRepository;
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
            $country = $this->countryRepository->countryList($request);
            $countryData = $country->toArray();
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (empty($countryData)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_COUNTRY_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $countryData);
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
