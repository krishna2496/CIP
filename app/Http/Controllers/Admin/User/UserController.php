<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Helpers\Helpers;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get request parameter from URL
        $searchString = Input::get('search','');
        $orderType = Input::get('order','asc');

        $userQuery = User::select('user_id','first_name','last_name','email')
        ->whereNull('deleted_at')
        ->whereStatus('1');

        // Check if search parameter passed in URL then search parameter will search in name field of tenant table.
        if (!empty($searchString)) {
            $userQuery->where(function($query) use($searchString){
                $query->orWhere('first_name', 'like', '%' . $searchString . '%');
                $query->orWhere('last_name', 'like', '%' . $searchString . '%');
            });
        }

        try {
            $userQuery->orderBy('user_id',$orderType)->paginate(10);            
            $userList = $userQuery->paginate(10);
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                        config('errors.custom_error_code.ERROR_40018'), 
                                        config('errors.custom_error_message.40018'));
			
        }

        if (count($userList)>0) {
            // Set response data
            $apiData = $userList;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_USER_LIST_SUCCESS');
            return Helpers::response($apiData, $apiStatus, $apiMessage);
        } else {
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_NO_DATA_FOUND');

            return Helpers::response('', $apiStatus, $apiMessage);
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
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
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
        ]);

        // If request parameter have any error
        if ($validator->fails()) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
										config('errors.status_type.HTTP_STATUS_TYPE_422'),
										config('errors.custom_error_code.ERROR_20010'),
										$validator->errors()->first());
        }

        try {

            // Create new user
            $user = User::create($request->toArray());

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_USER_CREATE_SUCCESS');    
            return Helpers::response($apiData, $apiStatus, $apiMessage);

        } catch (\Exception $e) {

            // Error for duplicate user name, trying to store in database.
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_20002'), 
										config('errors.custom_error_message.20002'));
				
            } else { // Any other error occured when trying to insert data into database for tenant.
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_20004'), 
										config('errors.custom_error_message.20004'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
			$user = User::findorFail($id);
            $user->delete();

            // Set response data
            $apiStatus = 200;            
            $apiMessage = config('messages.success_message.MESSAGE_USER_DELETE_SUCCESS');
            return Helpers::response('', $apiStatus, $apiMessage);

        } catch(\Exception $e){
            
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
										config('errors.status_type.HTTP_STATUS_TYPE_403'), 
										config('errors.custom_error_code.ERROR_20006'), 
										config('errors.custom_error_message.20006'));

        }
    }
}
