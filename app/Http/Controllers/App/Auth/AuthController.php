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
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param App\Helpers\Helpers $helpers
     * @param  Illuminate\Helpers\LanguageHelper $languageHelper
     * @param App\Repositories\User\UserRepository $userRepository
     * @return void
     */
    public function __construct(
        Request $request,
        ResponseHelper $responseHelper,
        TenantOptionRepository $tenantOptionRepository,
        Helpers $helpers,
        LanguageHelper $languageHelper,
        UserRepository $userRepository
    ) {
        $this->request = $request;
        $this->responseHelper = $responseHelper;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
        $this->userRepository = $userRepository;
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
        try {
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
            $userDetail = $user->where('email', $this->request->input('email'))->first();

            if (!$userDetail) {
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    config('constants.error_codes.ERROR_EMAIL_NOT_EXIST'),
                    trans('messages.custom_error_message.ERROR_EMAIL_NOT_EXIST')
                );
            }
            // Verify user's password
            if (!Hash::check($this->request->input('password'), $userDetail->password)) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVALID_PASSWORD'),
                    trans('messages.custom_error_message.ERROR_INVALID_PASSWORD')
                );
            }
            
            // Generate JWT token
            $data["token"] = $this->helpers->getJwtToken($userDetail->user_id);
            $data['user_id'] = isset($userDetail->user_id) ? $userDetail->user_id : '';
            $data['first_name'] = isset($userDetail->first_name) ? $userDetail->first_name : '';
            $data['last_name'] = isset($userDetail->last_name) ? $userDetail->last_name : '';
            $data['avatar'] = isset($userDetail->avatar) ? $userDetail->avatar :
            $this->helpers->getDefaultProfileImage($request);
            
            $apiData = $data;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_LOGGED_IN');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (TenantDomainNotFoundException $e) {
            throw $e;
        }
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
        try {
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

            $userDetail = $user->where('email', $request->get('email'))->first();

            $languages = $this->languageHelper->getLanguages($request);
            $language = $languages->where('language_id', $userDetail->language_id)->first();
            
            $languageCode = $language->code;
            config(['app.user_language_code' => $languageCode]);
            
            if (!$userDetail) {
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    config('constants.error_codes.ERROR_EMAIL_NOT_EXIST'),
                    trans('messages.custom_error_message.ERROR_EMAIL_NOT_EXIST')
                );
            }
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
            if (!$response == Password::RESET_LINK_SENT) {
                return $this->responseHelper->error(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                    config('constants.error_codes.ERROR_SEND_RESET_PASSWORD_LINK'),
                    trans('messages.custom_error_message.ERROR_SEND_RESET_PASSWORD_LINK')
                );
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_RESET_PASSWORD_INVALID_DATA'),
                trans('messages.custom_error_message.'
                .config('constants.error_codes.ERROR_RESET_PASSWORD_INVALID_DATA'))
            );
        }
    }

    /**
     * reset_password_link_expiry - check is reset password link is expired or not
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function passwordReset(Request $request): JsonResponse
    {
        try {
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
            $record = PasswordReset::where('email', $request->get('email'))
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
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_RESET_PASSWORD_INVALID_DATA'),
                trans('messages.custom_error_message.'
                .config('constants.error_codes.ERROR_RESET_PASSWORD_INVALID_DATA'))
            );
        }
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
        $passwordBrokerManager = new PasswordBrokerManager(app());
        return $passwordBrokerManager->broker();
    }
    
    /**
     * Change password from user edit profile page
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
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
            $newToken = ($passwordChange) ? $this->helpers->getJwtToken($request->auth->user_id) : '';
            
            // Send response
            $apiStatus = Response::HTTP_OK;
            $apiData = array('token' => $newToken);
            $apiMessage = trans('messages.success.MESSAGE_PASSWORD_CHANGE_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
    }
}
