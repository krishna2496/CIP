<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\Country\CountryRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use Illuminate\Support\Facades\Input;
use  PDOException;
use App\Helpers\ResponseHelper;

class CountryController extends Controller
{
    /**
     * @var App\Repositories\Country\CountryRepository 
     */
    private $country;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CountryRepository $country, Response $response)
    {
         $this->country = $country;
         $this->response = $response;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request):JsonResponse
    {
       
        try { 
            $country = $this->country->countryList($request);
            $countryData = $country->toArray();
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = (empty($countryData)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') : trans('messages.success.MESSAGE_COUNTRY_LISTING');
            return ResponseHelper::success($apiStatus, $apiMessage, $countryData);                  
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
