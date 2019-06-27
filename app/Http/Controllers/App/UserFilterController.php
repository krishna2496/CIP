<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\UserFilter\UserFilterRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use PDOException;
use App\Helpers\ResponseHelper;

class UserFilterController extends Controller
{
    /**
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $filters;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserFilterRepository $filters, Response $response)
    {
        $this->filters = $filters;
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
            // Get data of user's filter
            $filters = $this->filters->userFilter($request);

            if ($filters->count() == 0) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return ResponseHelper::success($apiStatus, $apiMessage);
            }

            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_CMS_LIST_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage, $filters->toArray());
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
