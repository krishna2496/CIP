<?php

namespace App\Http\Controllers\App\Auth;

use Validator;
use DB;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Config;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Models\PasswordReset;
use Carbon\Carbon;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use App\Helpers\LanguageHelper;
use App\Exceptions\TenantDomainNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\User\UserRepository;
use App\Events\User\UserActivityLogEvent;

class AuthController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * The response instance.
     *
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;

    /*
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;
    
    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;

    /**
     * @var App\Models\PasswordReset
     */
    private $passwordReset;
    
    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param App\Helpers\Helpers $helpers
     * @param Illuminate\Helpers\LanguageHelper $languageHelper
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Models\PasswordReset $passwordReset
     * @return void
     */
    public function __construct(
        Request $request,
        ResponseHelper $responseHelper,
        TenantOptionRepository $tenantOptionRepository,
        Helpers $helpers,
        LanguageHelper $languageHelper,
        UserRepository $userRepository,
        PasswordReset $passwordReset
    ) {
        $this->request = $request;
        $this->responseHelper = $responseHelper;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
        $this->userRepository = $userRepository;
        $this->passwordReset = $passwordReset;
        $this->passwordBrokerManager = new PasswordBrokerManager(app());
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function authenticate(User $user, Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_DETAIL'),
                $validator->errors()->first()
            );
        }
        
        // Fetch user by email address
        $userDetail = $user->with('timezone')->where('email', $this->request->input('email'))->first();

        if (!$userDetail) {
            return $this->responseHelper->error(
                Response::HTTP_FORBIDDEN,
                Response::$statusTexts[Response::HTTP_FORBIDDEN],
                config('constants.error_codes.ERROR_INVALID_EMAIL_OR_PASSWORD'),
                trans('messages.custom_error_message.ERROR_INVALID_EMAIL_OR_PASSWORD')
            );
        }
        // Verify user's password
        if (!Hash::check($this->request->input('password'), $userDetail->password)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_EMAIL_OR_PASSWORD'),
                trans('messages.custom_error_message.ERROR_INVALID_EMAIL_OR_PASSWORD')
            );
        }
        
        // Generate JWT token
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $data["token"] = $this->helpers->getJwtToken($userDetail->user_id, $tenantName);
        $data['user_id'] = isset($userDetail->user_id) ? $userDetail->user_id : '';
        $data['first_name'] = isset($userDetail->first_name) ? $userDetail->first_name : '';
        $data['last_name'] = isset($userDetail->last_name) ? $userDetail->last_name : '';
        $data['country_id'] = isset($userDetail->country_id) ? $userDetail->country_id : '';
        $data['avatar'] = ((isset($userDetail->avatar)) && $userDetail->avatar !="") ? $userDetail->avatar :
        $this->helpers->getUserDefaultProfileImage($tenantName);
        $data['cookie_agreement_date'] = isset($userDetail->cookie_agreement_date) ?
                                         $userDetail->cookie_agreement_date : '';
        $data['email'] = ((isset($userDetail->email)) && $userDetail->email !="") ? $userDetail->email : '';
        $data['timezone'] = ((isset($userDetail->timezone)) && $userDetail->timezone !="") ?
        $userDetail->timezone['timezone'] : '';
        
        $apiData = $data;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_USER_LOGGED_IN');

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.AUTH'),
            config('constants.activity_log_actions.LOGIN'),
            config('constants.activity_log_user_types.REGULAR'),
            $userDetail->email,
            get_class($this),
            null,
            $userDetail->user_id
        ));
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Forgot password - Send Reset password link to user's email address
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function requestPasswordReset(User $user, Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'email' => 'required|email'
        ]);
        
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_RESET_PASSWORD_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        $userDetail = $this->userRepository->findUserByEmail($request->get('email'));
        
        if (!$userDetail) {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                config('constants.error_codes.ERROR_EMAIL_NOT_EXIST'),
                trans('messages.custom_error_message.ERROR_EMAIL_NOT_EXIST')
            );
        }

        $language = $this->languageHelper->getLanguageDetails($request);
        $languageCode = $language->code;
        config(['app.user_language_code' => $languageCode]);
        
        $refererUrl = $this->helpers->getRefererFromRequest($request);
        config(['app.mail_url' => $refererUrl.'/reset-password/']);

        //set tenant logo
        $tenantLogo = $this->tenantOptionRepository->getOptionWithCondition(['option_name' => 'custom_logo']);
        config(['app.tenant_logo' => $tenantLogo->option_value]);
    
        // Verify email address and send reset password link
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        
        // If reset password link didn't sent
        if (!$response === $this->passwordReset->RESET_LINK_SENT) {
            return $this->responseHelper->error(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                config('constants.error_codes.ERROR_SEND_RESET_PASSWORD_LINK'),
                trans('messages.custom_error_message.ERROR_SEND_RESET_PASSWORD_LINK')
            );
        }

        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS');

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.AUTH'),
            config('constants.activity_log_actions.PASSWORD_RESET_REQUEST'),
            config('constants.activity_log_user_types.REGULAR'),
            $userDetail->email,
            get_class($this),
            $request->toArray(),
            $userDetail->user_id
        ));
        
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * reset_password_link_expiry - check is reset password link is expired or not
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function passwordReset(Request $request): JsonResponse
    {
        $request->merge(['token'=>$request->reset_password_token]);
    
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|min:8|same:password',
        ]);
        
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_DETAIL'),
                $validator->errors()->first()
            );
        }

        //get record of user by checking password expiry time
        $record = $this->passwordReset->where('email', $request->get('email'))
        ->where(
            'created_at',
            '>',
            Carbon::now()->subHours(config('constants.FORGOT_PASSWORD_EXPIRY_TIME'))
        )->first();
        
        //if record not found
        if (!$record) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_RESET_PASSWORD_LINK'),
                trans('messages.custom_error_message.ERROR_INVALID_RESET_PASSWORD_LINK')
            );
        }
        if (!Hash::check($request->get('token'), $record->token)) {
            //invalid hash
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_INVALID_RESET_PASSWORD_LINK'),
                trans('messages.custom_error_message.ERROR_INVALID_RESET_PASSWORD_LINK')
            );
        }
        
        // Reset the password
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );
    
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_PASSWORD_CHANGE_SUCCESS');

        $userDetail = $this->userRepository->findUserByEmail($request->get('email'));

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.AUTH'),
            config('constants.activity_log_actions.PASSWORD_RESET'),
            config('constants.activity_log_user_types.REGULAR'),
            $userDetail->email,
            get_class($this),
            $request->toArray(),
            $userDetail->user_id
        ));
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        return $request->only('email', 'password', 'password_confirmation', 'token');
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return $this->passwordBrokerManager->broker();
    }
    
    /**
     * Change password from user edit profile page
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'old_password' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
        ]);
        
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_DETAIL'),
                $validator->errors()->first()
            );
        }

        $isValidOldPassword = Hash::check($request->old_password, $request->auth->password);
        if (!$isValidOldPassword) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_DETAIL'),
                trans('messages.custom_error_message.ERROR_OLD_PASSWORD_NOT_MATCHED')
            );
        }
        
        // Update password
        $passwordChange = $this->userRepository->changePassword($request->auth->user_id, $request->password);
        
        // Get new token
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $newToken = ($passwordChange) ? $this->helpers->getJwtToken($request->auth->user_id, $tenantName) : '';
        
        // Send response
        $apiStatus = Response::HTTP_OK;
        $apiData = array('token' => $newToken);
        $apiMessage = trans('messages.success.MESSAGE_PASSWORD_CHANGE_SUCCESS');

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.AUTH'),
            config('constants.activity_log_actions.PASSWORD_UPDATED'),
            config('constants.activity_log_user_types.REGULAR'),
            $request->auth->email,
            get_class($this),
            $request->toArray(),
            $request->auth->user_id
        ));

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        
        // Update password
        $passwordChange = $this->userRepository->changePassword($request->auth->user_id, $request->password);
        
        // Get new token
        $newToken = ($passwordChange) ? $this->helpers->getJwtToken($request->auth->user_id) : '';
        
        // Send response
        $apiStatus = Response::HTTP_OK;
        $apiData = array('token' => $newToken);
        $apiMessage = trans('messages.success.MESSAGE_PASSWORD_CHANGE_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
