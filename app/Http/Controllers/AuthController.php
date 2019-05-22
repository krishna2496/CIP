<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Config;

class AuthController extends ApiController {

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
            'exp' => time() + 60 * 60   // Expiration time
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
            $this->errorType = config('errors.type.ERROR_TYPE_422');
            $this->apiStatus = 422;
            $this->apiErrorCode = 40001;
            $this->apiMessage = $validator->errors()->first();
            return $this->errorResponse();
        }
        
        // Fetch user by email address
        $user = User::where('email', $this->request->input('email'))->first();

        if (!$user) {
            $this->errorType = config('errors.type.ERROR_TYPE_403');
            $this->apiStatus = 403;
            $this->apiErrorCode = 40002;
            $this->apiMessage = config('errors.code.40002');
            return $this->errorResponse();
        }
        
        // Verify user's password
        if (!Hash::check($this->request->input('password'), $user->password)) {
            $this->errorType = config('errors.type.ERROR_TYPE_422');
            $this->apiStatus = 422;
            $this->apiErrorCode = 40004;
            $this->apiMessage = config('errors.code.40004');
            return $this->errorResponse();
        }
        
        // Generate JWT token
        $data["token"] = $this->jwt($user);
        $this->apiData = $data;
        $this->apiStatus = app('Illuminate\Http\Response')->status();
        $this->apiMessage = 'Authentication token generated successfully.';
        return $this->response();
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
            $this->errorType = config('errors.type.ERROR_TYPE_422');
            $this->apiStatus = 422;
            $this->apiErrorCode = 40001;
            $this->apiMessage = $validator->errors()->first();
            return $this->errorResponse();
        }

        // Fetch user by email address
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            $this->errorType = config('errors.type.ERROR_TYPE_403');
            $this->apiStatus = 403;
            $this->apiErrorCode = 40002;
            $this->apiMessage = config('errors.code.40002');
            return $this->errorResponse();
        }

        // Verify email address and send reset password link        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        // If reset password link didn't sent
        if (!$response == Password::RESET_LINK_SENT) {
            $this->errorType = config('errors.type.ERROR_TYPE_500');
            $this->apiStatus = 500;
            $this->apiErrorCode = 40006;
            $this->apiMessage = config('errors.code.40006');
            return $this->errorResponse();
        }

        $this->apiStatus = app('Illuminate\Http\Response')->status();
        $this->apiMessage = 'Reset Password link is sent to your email account,link will expire in ' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' hour';
        return $this->response();
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