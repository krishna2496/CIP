<?php
namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\User;
use App\Helpers\{Helpers, ResponseHelper, DatabaseHelper};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class UserRepository implements UserInterface
{
    public $user;
    
    private $response;

    public function __construct(User $user, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
    }
    
    public function store(Request $request)
    {
        try {
            // Connect master database to get language details
            DatabaseHelper::switchDatabaseConnection('mysql', $request);
            $languages = DB::table('language')->get();
            
            // Connect tenant database
            DatabaseHelper::switchDatabaseConnection('tenant', $request);

            // Server side validataions
            $validator = Validator::make($request->toArray(), $this->user->rules);

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_20022'),
                    $validator->errors()->first()
                );
            }
            
            $userData = array('first_name' => $request->first_name,
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
                              'linked_in_url' => $request->linked_in_url);
            
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
    
    public function update(Request $request, int $id)
    {
    }
    
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
}
