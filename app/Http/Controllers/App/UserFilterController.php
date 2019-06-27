<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\UserFilter\UserFilterRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use App\Traits\RestExceptionHandlerTrait;
use PDOException;
use App\Helpers\ResponseHelper;

class UserFilterController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $filters;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserFilterRepository $filters, 
        ResponseHelper $responseHelper)
    {
        $this->filters = $filters;
        $this->responseHelper = $responseHelper;
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
            $filterData = $filters->toArray();
            $apiStatus = Response::HTTP_OK;
            return $this->responseHelper->success($apiStatus,'', $filterData);
        } catch (\PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }
}
