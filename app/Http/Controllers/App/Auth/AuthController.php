<?php

namespace App\Http\Controllers\App\Auth;

use Validator;
use DB;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

class AuthController extends Controller
{

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
     * The response instance.
     *
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;
    
    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @return void
     */
    public function __construct(
        Request $request,
        ResponseHelper $responseHelper,
        TenantOptionRepository $tenantOptionRepository
    ) {
        $this->request = $request;
        $this->responseHelper = $responseHelper;
        $this->tenantOptionRepository = $tenantOptionRepository;
    }

    /**
     * Create a new token.
     *
     * @param \App\User $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt",       // Issuer of the token
            'sub' => $user->user_id,    // Subject of the token
            'iat' => time(),            // Time when JWT was issued.
            'exp' => time() + 60 * 60,  // Expiration time
            'fqdn' => 'tatva'
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function authenticate(User $user, Request $request)
    {

        // Server side validataions
        $validator = Validator::make($request->toArray(), $user->loginRules);

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_40001'),
                $validator->errors()->first()
            );
        }
        
        // Fetch user by email address
        $userDetail = $user->where('email', $this->request->input('email'))->first();

        if (!$userDetail) {
            return $this->responseHelper->error(
                trans('messages.status_code.HTTP_STATUS_FORBIDDEN'),
                trans('messages.status_type.HTTP_STATUS_TYPE_403'),
                trans('messages.custom_error_code.ERROR_40002'),
                trans('messages.custom_error_message.40002')
            );
        }
        
        // Verify user's password
        if (!Hash::check($this->request->input('password'), $userDetail->password)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_40004'),
                trans('messages.custom_error_message.40004')
            );
        }
        
        // Generate JWT token
        $data["token"] = $this->jwt($userDetail);
        $data['user_id'] = isset($userDetail->user_id) ? $userDetail->user_id : '';
        $data['first_name'] = isset($userDetail->first_name) ? $userDetail->first_name : '';
        $data['last_name'] = isset($userDetail->last_name) ? $userDetail->last_name : '';
        $data['avatar'] = isset($userDetail->avatar) ? $userDetail->avatar :'';
        
        $apiData = $data;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_USER_LOGGED_IN');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Forgot password - Send Reset password link to user's email address
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function requestPasswordReset(User $user, Request $request)
    {
             
        // Server side validataions
        $validator = Validator::make($request->toArray(), $user->resetPasswordRules);
        
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_40010'),
                $validator->errors()->first()
            );
        }

        // Fetch user by email address
        $userDetail = $user->where('email', $request->get('email'))->first();
        
        if (!$userDetail) {
            return $this->responseHelper->error(
                trans('messages.status_code.HTTP_STATUS_FORBIDDEN'),
                trans('messages.status_type.HTTP_STATUS_TYPE_403'),
                trans('messages.custom_error_code.ERROR_40002'),
                trans('messages.custom_error_message.40002')
            );
        }
        
        //get referer url using helper
        $refererUrl = Helpers::getRefererFromRequest($request);

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
                trans('messages.status_code.HTTP_STATUS_INTERNAL_SERVER_ERROR'),
                trans('messages.status_type.HTTP_STATUS_TYPE_500'),
                trans('messages.custom_error_code.ERROR_40006'),
                trans('messages.custom_error_message.40006')
            );
        }

        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage);
        ;
    }

    /**
     * reset_password_link_expiry - check is reset password link is expired or not
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function passwordReset(Request $request)
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
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_40011'),
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
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_40013'),
                trans('messages.custom_error_message.40013')
            );
        }

        if (!Hash::check($request->get('token'), $record->token)) {
            //invalid hash
            return $this->responseHelper->error(
                trans('messages.status_code.HTTP_STATUS_401'),
                trans('messages.status_type.HTTP_STATUS_TYPE_401'),
                trans('messages.custom_error_code.ERROR_40013'),
                trans('messages.custom_error_message.40013')
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
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
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
}
