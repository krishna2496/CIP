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

class AuthController extends ApiController 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) 
    {
        $this->request = $request;
    }
    
    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }
    
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user and Request data
     * @return mixed
     */
    public function authenticate(User $user,Request $request) 
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);
        
        if ($validator->fails()) {    
            $this->errorType = 'Bad Request';
            $this->apiStatus = 400;
            $this->apiCode = 61005; 
            $this->apiMessage = $validator->errors()->first(); // Set validation messages
            return $this->errorResponse();
        }
        // Find the user by email
        $user = User::where('email', $this->request->input('email'))->first();
       
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            $this->errorType = 'Bad Request';
            $this->apiStatus = 400;
            $this->apiCode = 61005;
            $this->apiMessage =  "Email does not exist.";
            return $this->errorResponse();
        }
        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            $data["token"] = $this->jwt($user);
            $this->apiData = (object)$data;
            $this->apiCode = app('Illuminate\Http\Response')->status();
            $this->apiStatus = true;
            $this->apiMessage = 'You are successfully logged in';
            return $this->response();
        }
    }
    
    /**
     * forgot password functionality.user will enter email address and System will check if user exist then an email with reset password link will be sent. 
     *  
     * @param  \App\User   $user and Request data
     * @return mixed
     */
    public function requestPasswordReset(User $user,Request $request) 
    {
        $validator = Validator::make($request->toArray(), [
            'email'     => 'required|email',
        ]);
        //if validation fail
        if ($validator->fails()) {    
            $this->errorType = 'Bad Request';
            $this->apiStatus = 400;
            $this->apiCode = 61005; 
            $this->apiMessage = $validator->errors()->first(); // Set validation messages
            return $this->errorResponse();
        }
        
        // Find the user by email
        $user = User::where('email',  $request->get('email'))->first();
        
        if (!$user) {
            //if user not exist
            $this->errorType = 'Bad Request';
            $this->apiStatus = 400;
            $this->apiCode = 61005; 
            $this->apiMessage = 'Email does not exist.'; // Set validation messages
            return $this->errorResponse();
        }
      
        // Verify the email and send the reset password link        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
       
        //if reset password link sent
        if($response == Password::RESET_LINK_SENT) {
            $data =array();
            $this->apiData = $data;
            $this->apiCode = app('Illuminate\Http\Response')->status();
            $this->apiStatus = true;
            $this->apiMessage = 'Reset Password link is sent to your email account,link will expire in ' .config('constants.FORGOT_PASSWORD_EXPIRY_TIME').' hour';
            return $this->response();
        } else {
            $this->errorType = 'Bad Request';
            $this->apiStatus = 400;
            $this->apiCode = 61005; 
            $this->apiMessage = "Something went's wrong"; // Set validation messages
            return $this->errorResponse();
        }
        
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