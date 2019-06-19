<?php
namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use App\Helpers\{Helpers, ResponseHelper};
use App\User;
use App\Models\UserSkill;
use Validator, PDOException, DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserInterface
{
    /**
     * @var App\User
     */
    public $user;

    /**
     * @var App\Model\UserSkill
     */
    public $userSkill;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new User repository instance.
     *
     * @param  App\User $user
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(User $user, UserSkill $userSkill, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
        $this->userSkill = $userSkill;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {
            // Connect master database to get language details
            Helpers::switchDatabaseConnection('mysql', $request);
            $languages = DB::table('language')->get();
            
            // Connect tenant database
            Helpers::switchDatabaseConnection('tenant', $request);

            // Server side validataions
            $validator = Validator::make($request->toArray(), $this->user->rules);

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100001'),
                    $validator->errors()->first()
                );
            }
            
            $userData = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                'timezone_id' => $request->timezone_id,
                'availability_id' => $request->availability_id,
                'why_i_volunteer' => $request->why_i_volunteer,
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'manager_name' => $request->manager_name,
                'city_id' => $request->city_id,
                'country_id' => $request->country_id,
                'profile_text' => $request->profile_text,
                'linked_in_url' => $request->linked_in_url,
                'language_id' => $request->language_id
            );
            
            // Create new user
            $user = $this->user->create($userData);

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
    }
    
    /**
     * Display a listing of all resources.
     *
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function userList(Request $request)
    {
        try {
            $userQuery = $this->user->with('city', 'country', 'timezone');
            
            if ($request->has('search')) {
                $userQuery->where(function ($query) use ($request) {
                    $query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
                    $query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
                });
            }
            if ($request->has('order')) {
                $orderDirection = $request->input('order', 'asc');
                $userQuery->orderBy('user_id', $orderDirection);
            }
            
            $userList = $userQuery->paginate(config('constants.PER_PAGE_LIMIT'));
            $responseMessage = (count($userList) > 0) ? trans('messages.success.MESSAGE_USER_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $userList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        try {
            $userDetail = $this->user->findUser($id);
            
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        try {
            $this->user->deleteUser($id);
            
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_USER_DELETED');

            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
        }
    }

    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function linkSkill(Request $request)
    {
        $userSkill = $this->userSkill;
        foreach ($request->skills as $value) {
            $skill = array(
                'user_id' => $request->user_id,
                'skill_id' => $value['skill_id'],
            );
            
            $skillData = $this->userSkill->findUserSkill($request->user_id, $value['skill_id']);
            if (count($skillData) < 1) {
                $userSkill = $this->userSkill->create($skill);
            }
            unset($skill);
        }
        return $userSkill;
    }
    
    /**
     * Remove the specified resource from storage
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function unlinkSkill(Request $request)
    {
        $userSkill = $this->userSkill; 
        foreach ($request->skills as $value) {
            $userSkill = $this->userSkill->deleteUserSkill($request->user_id, $value['skill_id']);
        }
        return $userSkill;
    }

    /**
     * Display a listing of specified resources.
     *
     * @param int $userId
     * @return mixed
     */
    public function userSkills(int $userId)
    {
        $userSkill = $this->userSkill->find($userId);   
        return $userSkill;
    }
}
