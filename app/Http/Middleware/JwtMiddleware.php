<?php
namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use App\User;
use App\Helpers\ResponseHelper;
use Firebase\JWT\ExpiredException;

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
        $token = ($request->hasHeader('token')) ? $request->header('token') : '';

        if(!$token) {
            // Unauthorized response if token not there
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNAUTHORIZED'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
                                        trans('messages.custom_error_code.ERROR_40012'), 
                                        trans('messages.custom_error_message.40012'));
        }
        try { 

            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
		} catch(\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNAUTHORIZED'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
                                        trans('messages.custom_error_code.ERROR_40014'), 
                                        trans('messages.custom_error_message.40014'));
        } catch(\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNAUTHORIZED'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
                                        trans('messages.custom_error_code.ERROR_40016'), 
                                        trans('messages.custom_error_message.40016'));
        } catch(\UnexpectedValueException $e) { 
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_TYPE_400'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_400'), 
                                        trans('messages.custom_error_code.ERROR_40016'), 
                                        trans('messages.custom_error_message.40016'));
        }

        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
