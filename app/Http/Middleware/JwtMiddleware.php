<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use App\User;
use App\Helpers\Helpers;
use DB;
class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next, $guard = null)
    {        
        $token = $request->get('token');
     
        if(!$token) {
            // Unauthorized response if token not there
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_40012'), 
                                        config('errors.custom_error_message.40012'));
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_400'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_400'), 
                                        config('errors.custom_error_code.ERROR_40014'), 
                                        config('errors.custom_error_message.40014'));
        } catch(Exception $e) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_400'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_400'), 
                                        config('errors.custom_error_code.ERROR_40016'), 
                                        config('errors.custom_error_message.40016'));
        }
        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
