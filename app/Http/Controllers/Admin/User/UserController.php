<?php
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;
use InvalidArgumentException;
use Illuminate\Validation\Rule;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;
use App\Events\User\UserActivityLogEvent;

//!  User controller
/*!
This controller is responsible for handling user listing, show, store, update, delete,
link skill, unlink skill and user skill listing operations.
 */
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
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var string
     */
    private $userApiKey;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\ResponseHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        Request $request
    ) {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->userApiKey = $request->header('php-auth-user');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
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
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            ["first_name" => "sometimes|required|max:16",
            "last_name" => "sometimes|required|max:16",
            "email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
            "password" => "required|min:8",
            "availability_id" => "sometimes|required|integer|exists:availability,availability_id,deleted_at,NULL",
            "timezone_id" => "sometimes|required|integer|exists:timezone,timezone_id,deleted_at,NULL",
            "language_id" => "sometimes|required|int",
            "city_id" => "integer|sometimes|required|exists:city,city_id,deleted_at,NULL",
            "country_id" => "integer|sometimes|required|exists:country,country_id,deleted_at,NULL",
            "profile_text" => "sometimes|required",
            "employee_id" => "max:16|
            unique:user,employee_id,NULL,user_id,deleted_at,NULL",
            "department" => "max:16",
            "linked_in_url" => "url|valid_linkedin_url",
            "why_i_volunteer" => "sometimes|required",
            ]
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
        
        // Check language id is set and valid or not
        if (isset($request->language_id)) {
            if (!$this->languageHelper->validateLanguageId($request)) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                    trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
                );
            }
        }
        
        
        // Create new user
        $user = $this->userRepository->store($request->all());

        // Check profile complete status
        $userData = $this->userRepository->checkProfileCompleteStatus($user->user_id, $request);

        // Set response data
        $apiData = ['user_id' => $user->user_id];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');
        
        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.USERS'),
            config('constants.activity_log_actions.CREATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $request->toArray(),
            null,
            $user->user_id
        ));
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Display the specified user detail.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $userDetail = $this->userRepository->find($id);
                
            $apiData = $userDetail->toArray();
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $apiData['avatar'] = ((isset($apiData['avatar'])) && $apiData['avatar'] !="") ? $apiData['avatar'] :
            $this->helpers->getUserDefaultProfileImage($tenantName);
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
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
                ["first_name" => "sometimes|required|max:16",
                "last_name" => "sometimes|required|max:16",
                "email" => [
                    "sometimes",
                    "required",
                    "email",
                    Rule::unique('user')->ignore($id, 'user_id')],
                "password" => "sometimes|required|min:8",
                "employee_id" => [
                    "sometimes",
                    "required",
                    "max:16",
                    Rule::unique('user')->ignore($id, 'user_id,deleted_at,NULL')],
                "department" => "sometimes|required|max:16",
                "linked_in_url" => "url|valid_linkedin_url",
                "why_i_volunteer" => "sometimes|required",
                "timezone_id" => "sometimes|required|integer|exists:timezone,timezone_id,deleted_at,NULL",
                "availability_id" => "sometimes|required|integer|exists:availability,availability_id,deleted_at,NULL",
                "city_id" => "sometimes|required|integer|exists:city,city_id,deleted_at,NULL",
                "country_id" => "sometimes|required|integer|exists:country,country_id,deleted_at,NULL"]
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
            
            // Check language id
            if (isset($request->language_id)) {
                if (!$this->languageHelper->validateLanguageId($request)) {
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                        trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
                    );
                }
            }

            // Update user
            $user = $this->userRepository->update($request->toArray(), $id);

            // Check profile complete status
            $userData = $this->userRepository->checkProfileCompleteStatus($user->user_id);
            
            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');
            
            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.USERS'),
                config('constants.activity_log_actions.UPDATED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $request->toArray(),
                null,
                $user->user_id
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
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
           
            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.USERS'),
                config('constants.activity_log_actions.DELETED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                [],
                null,
                $id
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function linkSkill(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'skills' => 'required',
                'skills.*.skill_id' => 'required|exists:skill,skill_id,deleted_at,NULL',
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
            $linkedSkills = $this->userRepository->linkSkill($request->toArray(), $id);
            
            foreach ($linkedSkills as $linkedSkill) {
                // Make activity log
                event(new UserActivityLogEvent(
                    config('constants.activity_log_types.USER_SKILL'),
                    config('constants.activity_log_actions.LINKED'),
                    config('constants.activity_log_user_types.API'),
                    $this->userApiKey,
                    get_class($this),
                    $request->toArray(),
                    null,
                    $linkedSkill['skill_id']
                ));
            }
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_CREATED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $userId
     * @return Illuminate\Http\JsonResponse
     */
    public function unlinkSkill(Request $request, int $userId): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make($request->toArray(), [
                'skills' => 'required',
                'skills.*.skill_id' => 'required|exists:skill,skill_id',
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

            $unlinkedIds = $this->userRepository->unlinkSkill($request->toArray(), $userId);

            foreach ($unlinkedIds as $unlinkedId) {
                // Make activity log
                event(new UserActivityLogEvent(
                    config('constants.activity_log_types.USER_SKILL'),
                    config('constants.activity_log_actions.UNLINKED'),
                    config('constants.activity_log_user_types.API'),
                    $this->userApiKey,
                    get_class($this),
                    $request->toArray(),
                    null,
                    $unlinkedId['skill_id']
                ));
            }
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_DELETED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
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
            $responseMessage = (count($skillList) > 0) ? trans('messages.success.MESSAGE_SKILL_LISTING')
             : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            return $this->responseHelper->success(Response::HTTP_OK, $responseMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
    }
}
