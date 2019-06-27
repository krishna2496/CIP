<?php
namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use App\User;
use App\Helpers\ResponseHelper;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Response;

class JwtMiddleware
{
    /**
     * Create a new middleware instance.
     *
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

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

        if (!$token) {
            // Unauthorized response if token not there
<<<<<<< HEAD
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNAUTHORIZED'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
                                        trans('messages.custom_error_code.ERROR_40012'), 
                                        trans('messages.custom_error_message.40012'));
=======
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_TOKEN_NOT_PROVIDED'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TOKEN_NOT_PROVIDED'))
            );
>>>>>>> f9fc8c4bf9cfe0c5d98d05f1fc778b6cc3ff206e
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
<<<<<<< HEAD
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
=======
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_TOKEN_EXPIRED'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TOKEN_EXPIRED'))
            );
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_IN_TOKEN_DECODE'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_IN_TOKEN_DECODE'))
            );
        } catch (\UnexpectedValueException $e) {
            return $this->responseHelper->error(
                Response::HTTP_BAD_REQUEST,
                Response::$statusTexts[Response::HTTP_BAD_REQUEST ],
                config('constants.error_codes.ERROR_IN_TOKEN_DECODE'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_IN_TOKEN_DECODE'))
            );
>>>>>>> f9fc8c4bf9cfe0c5d98d05f1fc778b6cc3ff206e
        }

        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
