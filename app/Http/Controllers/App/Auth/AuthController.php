<?php

namespace App\Http\Controllers\App\Auth;

use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Config;
use App\Helpers\Helpers;
use DB;
use App\PasswordReset;
use Carbon\Carbon;

class AuthController extends Controller {

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Create a new token.
     * 
     * @param \App\User $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt",       // Issuer of the token
            'sub' => $user->id,         // Subject of the token
            'iat' => time(),            // Time when JWT was issued. 
            'exp' => time() + 60 * 60,  // Expiration time
            'fqdn' => Helpers::getSubDomainFromRequest($this->request)
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
    public function authenticate(User $user, Request $request) {	        

        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_40001'), 
										$validator->errors()->first());
        }
        
        // Fetch user by email address
        $user = User::where('email', $this->request->input('email'))->first();

        if (!$user) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
										config('errors.status_type.HTTP_STATUS_TYPE_403'), 
										config('errors.custom_error_code.ERROR_40002'), 
										config('errors.custom_error_message.40002'));
        }
        
        // Verify user's password
        if (!Hash::check($this->request->input('password'), $user->password)) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_40004'), 
										config('errors.custom_error_message.40004'));
        }
        
        // Generate JWT token
        $data["token"] = $this->jwt($user);
        $apiData = $data;
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = 'You are successfully logged in';
        return Helpers::response($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Forgot password - Send Reset password link to user's email address
     *  
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function requestPasswordReset(User $user, Request $request) {
        
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
                'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
										config('errors.status_type.HTTP_STATUS_TYPE_422'), 
										config('errors.custom_error_code.ERROR_40010'), 
										$validator->errors()->first());
        }

        // Fetch user by email address
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
										config('errors.status_type.HTTP_STATUS_TYPE_403'), 
										config('errors.custom_error_code.ERROR_40002'), 
										config('errors.custom_error_message.40002'));
        }
        
        //get referer url using helper 
        $refererUrl = Helpers::getRefererFromRequest($request);
        config(['app.mail_url' => $refererUrl.'/reset-password/']);
       
        // Verify email address and send reset password link        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        // If reset password link didn't sent
        if (!$response == Password::RESET_LINK_SENT) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_500'), 
										config('errors.status_type.HTTP_STATUS_TYPE_500'), 
										config('errors.custom_error_code.ERROR_40006'), 
										config('errors.custom_error_message.40006'));
        }

        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = 'Reset Password link is sent to your email account,link will expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hours';
        return $this->response($apiStatus, $apiMessage);
    }

    /**
     * reset_password_link_expiry - check is reset password link is expired or not
     *  
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function passwordReset(Request $request) {
       
        $request->merge(['token'=>$request->reset_password_token]);
      
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|min:8|same:password',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_40011'), 
                                        $validator->errors()->first());
        }
 
        //get record of user by checking password expiry time
        $record = PasswordReset::where('email',$request->get('email'))->where('created_at','>',Carbon::now()->subHours(config('constants.FORGOT_PASSWORD_EXPIRY_TIME')))->first();
        
        //if record not found
        if(!$record){
            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_40012'), 
                                        config('errors.custom_error_message.40012'));
        }

        if(!Hash::check($request->get('token'), $record->token)){
            //invalid hash
            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_40012'), 
                                        config('errors.custom_error_message.40012'));
        }
       
         // Reset the password
        $response = $this->broker()->reset(
        $this->credentials($request),
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );

        $this->apiStatus = app('Illuminate\Http\Response')->status();
        $this->apiMessage = 'Your password has been changed successfully.';
        return $this->response();
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
    public function broker() {
        $passwordBrokerManager = new PasswordBrokerManager(app());
        return $passwordBrokerManager->broker();
    }

}
