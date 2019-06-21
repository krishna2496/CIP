<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use Illuminate\Support\Facades\Input;
use Validator, DB, PDOException;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CityController extends Controller
{
    /**
     * @var App\Repositories\City\CityRepository 
     */
    private $city;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CityRepository $city, Response $response)
    {
         $this->city = $city;
         $this->response = $response;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
       
        try { 
            $city = $this->city->cityList($request);
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = (empty($city)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') : trans('messages.success.MESSAGE_CITY_LISTING');
            return ResponseHelper::success($apiStatus, $apiMessage, $city);                  
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) { 
            throw new \Exception($e->getMessage());
        }
    }

}
