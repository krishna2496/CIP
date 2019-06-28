<?php
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Input;
use App\Models\City;
use App\Models\Country;
use App\Models\Timezone;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;
use InvalidArgumentException;
use PDOException;

class UserController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(UserRepository $userRepository, ResponseHelper $responseHelper)
    {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userRepository->userList($request);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($users->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_USER_LISTING');
            return $this->responseHelper->successWithPagination(Response::HTTP_OK, $apiMessage, $users);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
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
            $validator = Validator::make(
                $request->all(),
                ["first_name" => "required|max:16",
                "last_name" => "required|max:16",
                "email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
                "password" => "required",
                "city_id" => "required",
                "country_id" => "required",
                "profile_text" => "required",
                "employee_id" => "max:16",
                "department" => "max:16",
                "manager_name" => "max:16",
                "linked_in_url" => "url"]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            
            // Create new user
            $user = $this->userRepository->store($request->all());

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Display the specified user detail.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $userDetail = $this->userRepository->find($id);
                
            $apiData = $userDetail->toArray();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
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
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                ["first_name" => "max:16",
                "last_name" => "max:16",
                "email" => "email|unique:user,email,'. $id . ',user_id,deleted_at,NULL",
                "employee_id" => "max:16",
                "department" => "max:16",
                "manager_name" => "max:16",
                "linked_in_url" => "url"]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }

            // Update user
            $user = $this->userRepository->update($request->toArray(), $id);

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_USER_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function linkSkill(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'user_id' => 'required',
                'skills' => 'required',
                'skills.*.skill_id' => 'required|string',
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }

            $this->userRepository->linkSkill($request->toArray());

            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_CREATED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_USER_CUSTOM_FIELD_INVALID_DATA')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function unlinkSkill(Request $request): JsonResponse
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
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }

            $userSkill = $this->userRepository->unlinkSkill($request->toArray());
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_DELETED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $userId
     * @return Illuminate\Http\JsonResponse
     */
    public function userSkills(int $userId): JsonResponse
    {
        try {
            $skillList = $this->userRepository->userSkills($userId);

            // Set response data
            $apiData = (count($skillList) > 0) ? $skillList->toArray() : [];
            $responseMessage = (count($skillList) > 0) ? trans('messages.success.MESSAGE_USER_LISTING')
             : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            return $this->responseHelper->success(Response::HTTP_OK, $responseMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_SKILL_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_SKILL_NOT_FOUND')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
