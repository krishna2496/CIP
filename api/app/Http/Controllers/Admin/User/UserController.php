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
use App\Services\UserService;
use App\Services\TimesheetService;
use App\Repositories\Notification\NotificationRepository;

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
        NotificationRepository $notificationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->userService = $userService;
        $this->timesheetService = $timesheetService;
        $this->helpers = $helpers;
        $this->userApiKey = $request->header('php-auth-user');
        $this->notificationRepository = $notificationRepository;
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
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "first_name" => "sometimes|required|max:16",
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
                "expiry" => "sometimes|date|nullable",
                "status" => [
                    "sometimes",
                    Rule::in(config('constants.user_statuses'))
                ]
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

        $requestData = $request->toArray();
        $requestData['expiry'] = (isset($request->expiry)) && $request->expiry
            ? $request->expiry : null;
        $requestData['status'] = config('constants.user_statuses.ACTIVE');
        if (isset($request->status)) {
            $requestData['status'] = $request->status
                ? config('constants.user_statuses.ACTIVE')
                : config('constants.user_statuses.INACTIVE');
        }

        // Create new user
        $user = $this->userRepository->store($requestData);

        // Check profile complete status
        $userData = $this->userRepository->checkProfileCompleteStatus($user->user_id, $request);

        // Set response data
        $apiData = ['user_id' => $user->user_id];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');

        // Remove password before logging it
        $request->request->remove("password");

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
                [
                    "first_name" => "sometimes|required|max:16",
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
                    "country_id" => "sometimes|required|integer|exists:country,country_id,deleted_at,NULL",
                    "expiry" => "sometimes|date|nullable",
                    "status" => [
                        "sometimes",
                        Rule::in(config('constants.user_statuses'))
                    ]
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

            $requestData = $request->toArray();
            $requestData['expiry'] = (isset($request->expiry)) && $request->expiry
                ? $request->expiry : null;
            if (isset($request->status)) {
                $requestData['status'] = $request->status
                    ? config('constants.user_statuses.ACTIVE')
                    : config('constants.user_statuses.INACTIVE');
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
