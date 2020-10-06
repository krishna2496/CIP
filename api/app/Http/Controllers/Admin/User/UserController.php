<?php
namespace App\Http\Controllers\Admin\User;

use App\Events\User\UserActivityLogEvent;
use App\Exceptions\MaximumUsersReachedException;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Timezone\TimezoneRepository;
use App\Services\TimesheetService;
use App\Services\UserService;
use App\Traits\RestExceptionHandlerTrait;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Validator;

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
     * @var App\Services\UserService
     */
    private $timesheetService;

    /**
     * @var App\Services\TimesheetService
     */
    private $userService;

    /**
     * @var string
     */
    private $userApiKey;

    /**
     * @var App\Repositories\Notification\NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var App\Repositories\Timezone\TimezoneRepository
     */
    private $timezoneRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\ResponseHelper $languageHelper
     * @param App\Services\UserService $userService
     * @param App\Helpers\Helpers $helpers
     * @param Illuminate\Http\Request $request
     * @param App\Repositories\Notification\NotificationRepository $notificationRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        UserService $userService,
        TimesheetService $timesheetService,
        Helpers $helpers,
        Request $request,
        NotificationRepository $notificationRepository,
        TimezoneRepository $timezoneRepository
    ) {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->userService = $userService;
        $this->timesheetService = $timesheetService;
        $this->helpers = $helpers;
        $this->userApiKey = $request->header('php-auth-user');
        $this->notificationRepository = $notificationRepository;
        $this->timezoneRepository = $timezoneRepository;
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
     * Display specific user content statistics
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function contentStatistics(Request $request, $userId): JsonResponse
    {

        try {
            $user = $this->userService->findById($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }

        $statistics = $this->userService->statistics($user, $request->all());

        $data = $statistics;
        $status = Response::HTTP_OK;
        $message = trans('messages.success.MESSAGE_TENANT_USER_CONTENT_STATISTICS_SUCCESS');

        return $this->responseHelper->success($status, $message, $data);

    }

    /**
     * Get user's volunteer summary
     *
     * @param \Illuminate\Http\Request $request
     * @param String $userId
     *
     * @return JsonResponse
     */
    public function volunteerSummary(Request $request, $userId): JsonResponse
    {
        try {
            $user = $this->userService->findById($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }

        $data = $this->userService->volunteerSummary($user, $request->all());

        $status = Response::HTTP_OK;
        $message = trans('messages.success.MESSAGE_TENANT_USER_VOLUNTEER_SUMMARY_SUCCESS');
        return $this->responseHelper->success($status, $message, $data);
    }

    /**
     * Display specific user timesheet summary
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function timesheetSummary(Request $request, $userId): JsonResponse
    {

        try {
            $user = $this->userService->findById($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }

        $data = $this->timesheetService->summary($user, $request->all());
        $status = Response::HTTP_OK;
        $message = trans('messages.success.MESSAGE_TENANT_USER_TIMESHEET_SUMMARY_SUCCESS');

        return $this->responseHelper->success($status, $message, $data);

    }

    /**
     * Display specific user timesheet
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function timesheet(Request $request, $userId): JsonResponse
    {

        try {
            $user = $this->userRepository->find($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }

        $timesheets = $this->userRepository->getMissionTimesheet($request, $userId);

        $data = $timesheets->toArray();
        $status = $timesheets->isEmpty() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        $message = $timesheets->isEmpty() ? trans('messages.success.MESSAGE_TENANT_USER_TIMESHEET_EMPTY') : trans('messages.success.MESSAGE_TENANT_USER_TIMESHEET_SUCCESS');

        return $this->responseHelper->success($status, $message, $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $fieldsToValidate = [
            "first_name" => "sometimes|required|max:60",
            "last_name" => "sometimes|required|max:60",
            "email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
            "password" => "required|min:8",
            "availability_id" => "sometimes|required|integer|exists:availability,availability_id,deleted_at,NULL",
            "timezone_id" => "sometimes|required|integer|exists:timezone,timezone_id,deleted_at,NULL",
            "language_id" => "sometimes|required|int",
            "city_id" => "sometimes|integer|exists:city,city_id,deleted_at,NULL",
            "country_id" => "integer|sometimes|required|exists:country,country_id,deleted_at,NULL",
            "profile_text" => "sometimes|required",
            "employee_id" => "max:60|
            unique:user,employee_id,NULL,user_id,deleted_at,NULL",
            "department" => "max:60",
            "linked_in_url" => "url|valid_linkedin_url",
            "why_i_volunteer" => "sometimes|required",
            "expiry" => "sometimes|date|nullable",
            "status" => [
                "sometimes",
                Rule::in(config('constants.user_statuses'))
            ],
            "position" => "sometimes|nullable",
            "title" => "max:60"
        ];

        if (is_array($request->skills)) {
            $fieldsToValidate['skills.*.skill_id'] = 'required_with:skills|integer|exists:skill,skill_id,deleted_at,NULL';
        }

        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            $fieldsToValidate
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

        if (!isset($request->timezone_id)) {
            $defaultTimezone = env('DEFAULT_TIMEZONE', 'Europe/Paris');
            $timezone = $this->timezoneRepository->getTenantTimezoneByCode($defaultTimezone);
            $request->merge(['timezone_id' => $timezone->timezone_id]);
        }

        $request->expiry = (isset($request->expiry) && $request->expiry)
            ? $request->expiry : null;

        // Create new user
        try {
            $user = $this->userService->store($request->toArray());
        } catch (MaximumUsersReachedException $e) {
            return $this->responseHelper->error(
                Response::HTTP_BAD_REQUEST,
                Response::$statusTexts[Response::HTTP_BAD_REQUEST],
                config('constants.error_codes.ERROR_MAXIMUM_USERS_REACHED'),
                trans('messages.custom_error_message.ERROR_MAXIMUM_USERS_REACHED')
            );
        }

        // Check profile complete status
        $userData = $this->userRepository->checkProfileCompleteStatus($user->user_id, $request);

        // Set response data
        $apiData = ['user_id' => $user->user_id];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');

        // Remove password before logging it
        $request->request->remove("password");

        if ($request->skills && is_array($request->skills)) {
            $this->userRepository->linkSkill($request->toArray(), $user->user_id);
        }

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
            $apiData['avatar'] = ((isset($apiData['avatar'])) && $apiData['avatar'] !="") ? $apiData['avatar'] : '';
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
        $requestData = $request->toArray();
        $fieldsToValidate = $this->getFieldsTovalidate($id, $requestData);

        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                $fieldsToValidate
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
                if ($request->language_id) {
                    if (!$this->languageHelper->validateLanguageId($request)) {
                        return $this->responseHelper->error(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                            config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                            trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
                        );
                    }
                }
            }

            $requestData['expiry'] = (isset($request->expiry)) && $request->expiry
            ? $request->expiry : null;
            if (isset($request->status)) {
                $requestData['status'] = $request->status
                    ? config('constants.user_statuses.ACTIVE')
                    : config('constants.user_statuses.INACTIVE');
            }


            $userDetail = $this->userRepository->find($id);

            // Skip updaing pseudonymize fields
            if ($userDetail->pseudonymize_at && $userDetail->pseudonymize_at !== '0000-00-00 00:00:00') {
                $pseudonymizeFields = $this->helpers->getSupportedFieldsToPseudonymize();
                foreach ($pseudonymizeFields as $field) {
                    if (array_key_exists($field, $requestData)) {
                        unset($requestData[$field]);
                    }
                }


                if (array_key_exists('pseudonymize_at', $requestData)) {
                    unset($requestData['pseudonymize_at']);
                }
            }

            // Set user status to inactive when pseudonymized
            if (($userDetail->pseudonymize_at === '0000-00-00 00:00:00' || $userDetail->pseudonymize_at === null) &&
                array_key_exists('pseudonymize_at', $requestData)
            ) {
                $requestData['status'] = config('constants.user_statuses.INACTIVE');
            }

            if (isset($requestData['avatar'])) {
                $requestData['avatar'] = empty($requestData['avatar']) ? null : $requestData['avatar'];
            }

            // Update user
            $user = $this->userRepository->update($requestData, $id);

            // Check profile complete status
            $userData = $this->userRepository->checkProfileCompleteStatus($user->user_id, $request);

            // Set response data
            $apiData = ['user_id' => $user->user_id];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');

            // Remove password before logging it
            $request->request->remove("password");

            if (is_array($request->skills)) {
                $this->userRepository->deleteSkills($id);
                if ($request->skills) {
                    $this->userRepository->linkSkill($request->toArray(), $id);
                }
            }

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

    private function getFieldsTovalidate($id, $requestData)
    {
        $fieldsToValidate = [
            "first_name" => "sometimes|required|max:60",
            "last_name" => "sometimes|required|max:60",
            "email" => [
                "sometimes",
                "required",
                "email",
                Rule::unique('user')->ignore($id, 'user_id,deleted_at,NULL')
            ],
            "password" => "sometimes|required|min:8",
            "employee_id" => [
                "sometimes",
                "required",
                "max:60",
                Rule::unique('user')->ignore($id, 'user_id,deleted_at,NULL')],
            "department" => "sometimes|required|max:60",
            "linked_in_url" => "url|valid_linkedin_url",
            "why_i_volunteer" => "sometimes|required",
            "timezone_id" => "sometimes|required|integer|exists:timezone,timezone_id,deleted_at,NULL",
            "availability_id" => "sometimes|required|integer|exists:availability,availability_id,deleted_at,NULL",
            "city_id" => "sometimes|integer|exists:city,city_id,deleted_at,NULL",
            "country_id" => "sometimes|required|integer|exists:country,country_id,deleted_at,NULL",
            "expiry" => "sometimes|date|nullable",
            "status" => [
                "sometimes",
                Rule::in(config('constants.user_statuses'))
            ],
            "position" => "sometimes|nullable",
            "title" => "max:60"
        ];

        if (array_key_exists('skills', $requestData)) {
            $fieldsToValidate['skills'] = 'array';
            $fieldsToValidate['skills.*.skill_id'] = 'required_with:skills|integer|exists:skill,skill_id,deleted_at,NULL';
        }

        $pseudomizeFields = $this->helpers->getSupportedFieldsToPseudonymize();
        if (array_key_exists('pseudonymize_at', $requestData)) {
            $user = $this->userService->findById($id);
            if ($user->pseudonymize_at === '0000-00-00 00:00:00' || $user->pseudonymize_at === null) {
                foreach ($pseudomizeFields as $field) {
                    $rules = ['sometimes', 'required'];

                    if ($field === 'email') {
                        $fieldsToValidate[$field] = array_push($rules, 'email');
                    } else if ($field === 'linked_in_url') {
                        $fieldsToValidate[$field] = array_push($rules, 'valid_linkedin_url');
                    }

                    $fieldsToValidate[$field] = implode('|', $rules);
                }
            }
        }

        $nullableFields = [
            'employee_id',
            'department',
            'linked_in_url',
            'why_i_volunteer',
            'availability_id',
            'city_id',
            'country_id',
            'profile_text',
            'position'
        ];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $requestData) && !$requestData[$field]) {
                $fieldsToValidate[$field] = 'nullable';
            }
        }

        return $fieldsToValidate;
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
            $this->notificationRepository->deleteAllNotifications($id);

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
