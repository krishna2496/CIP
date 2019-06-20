<?php
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Timezone;
use App\Helpers\ResponseHelper;
use Validator;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * @var App\Models\User
     */
    private $user;
    
    private $response;
    
    /**
     * Create a new User controller instance.
     *
     * @param  App\Repositories\User\UserRepository $user
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
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $this->user->store($request);
        try {
            // Server side validataions
            $validator = Validator::make($request->toArray(), 
                [
                "first_name" => "required|max:16",
                "last_name" => "required|max:16",
                "email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
                "password" => "required",
                "city_id" => "required",
                "country_id" => "required",
                "profile_text" => "required",
                "employee_id" => "max:16",
                "department" => "max:16",
                "manager_name" => "max:16",
                "linked_in_url" => "url"
                ]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100010'),
                    $validator->errors()->first()
                );
            }
            
            // Create new user
            $user = $this->user->store($request);

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');
            
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display the specified user detail.
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        try {
            $userDetail = $this->user->find($id);
                
            $apiData = $userDetail->toArray();
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
            
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            // Update user
            $user = $this->user->update($request, $id);

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');
            
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->user->delete($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function linkSkill(Request $request)
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'user_id' => 'required',
                'skills' => 'required',
                'skills.*.skill_id' => 'required|string',
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100002'),
                    $validator->errors()->first()
                );
            }

            $userSkill = $this->user->linkSkill($request);

            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_CREATED');
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_CREATED');
            
            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function unlinkSkill(Request $request)
    {
        try {
            // Server side validataions
            $validator = Validator::make($request->toArray(), [
                'user_id' => 'required',
                'skills' => 'required',
                'skills.*.skill_id' => 'required|string',
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100002'),
                    $validator->errors()->first()
                );
            }

            $userSkill = $this->user->unlinkSkill($request);
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_DELETED');
            
            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function userSkills(int $userId)
    {
        try {
            $skillList = $this->user->userSkills($userId);
            $responseMessage = (count($skillList) > 0) ? trans('messages.success.MESSAGE_USER_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $skillList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }
}
