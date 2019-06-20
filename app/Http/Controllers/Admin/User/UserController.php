<?php
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\{Request, Response, JsonResponse};
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Input;
use App\Models\{City, Country, Timezone};
use App\Helpers\ResponseHelper;
use Validator, DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
	private $user;
    
	/**
     * @var Response
     */
	private $response;
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
			$users = $this->user->userList($request);
			
			// Set response data
            $apiStatus = $this->response->status();
            $apiMessage = ($users->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') : trans('messages.success.MESSAGE_USER_LISTING');
			return ResponseHelper::successWithPagination($this->response->status(), $apiMessage, $users);
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
			// Server side validataions
			$validator = Validator::make($request->toArray(), ["first_name" => "required|max:16",
																"last_name" => "required|max:16",
																"email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
																"password" => "required",
																"city_id" => "required",
																"country_id" => "required",
																"profile_text" => "required",
																"employee_id" => "max:16",
																"department" => "max:16",
																"manager_name" => "max:16",
																"linked_in_url" => "url"]);

			// If request parameter have any error
			if ($validator->fails()) {
				return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
											trans('messages.status_type.HTTP_STATUS_TYPE_422'),
											trans('messages.custom_error_code.ERROR_100010'),
											$validator->errors()->first());
			}
			
			// Create new user
			$user = $this->user->store($request);

			// Set response data
			$apiData = ['user_id' => $user->user_id];
			$apiStatus = $this->response->status();
			$apiMessage = trans('messages.success.MESSAGE_USER_CREATED');    
			
			return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
			
        } catch(PDOException $e) {
			
			throw new PDOException($e->getMessage());
			
		} catch(\Exception $e) {
			
			throw new \Exception($e->getMessage());
			
		}
    }

    /**
     * Display the specified user detail.
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $id): JsonResponse
    {
       try {         
			$userDetail = $this->user->find($id);
				
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

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
			// Update user
			$user = $this->user->update($request, $id);

			// Set response data
			$apiData = ['user_id' => $user->user_id];
			$apiStatus = $this->response->status();
			$apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');    
			
			return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
			
        } catch(ModelNotFoundException $e){
			
			throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
			
        } catch(PDOException $e) {
			
			throw new PDOException($e->getMessage());
			
		} catch(\Exception $e) {
			
			throw new \Exception($e->getMessage());
			
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy(int $id)
    {
        try {  
            $user = $this->user->delete($id);
            
			// Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_USER_DELETED');
            return ResponseHelper::success($apiStatus, $apiMessage);            
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
        }
    }
}
