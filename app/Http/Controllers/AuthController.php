<?php
namespace App\Http\Controllers;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;

class AuthController extends BaseController 
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
       
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);
 
        // Find the user by email
        $user = User::where('email',  $request->get('email'))->first();
        
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
       
        // Verify the password and generate the token
        if (Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'token' => $this->jwt($user)
            ], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }
    
    /**
     * forgot password functionality.user will enter email address and System will check if user exist then an email with reset password link will be sent.  
     * @param  \App\User   $user and Request data
     * @return mixed
     */
    public function requestPasswordReset(User $user,Request $request) 
    {
       
        $this->validate($request, [
            'email'     => 'required|email',
        ]);
      
        // Find the user by email
        $user = User::where('email',  $request->get('email'))->first();
        
        if (!$user) {
            //if user not exist
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
      
        // Verify the email and send the reset password link        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
       
        // Bad Request response
        return $response == Password::RESET_LINK_SENT
            ? response()->json(true)
            : response()->json(false);
    }
    
    public function resetPassword(User $user,Request $request) 
    {
        exit("sdg");
        $this->validate($request, [
            'email'     => 'required|email',
        ]);
      
        // Find the user by email
        $user = User::where('email',  $request->get('email'))->first();
        
        if (!$user) {
            //if user not exist
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
      
        // Verify the email and send the reset password link
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        exit("dsg");
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
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