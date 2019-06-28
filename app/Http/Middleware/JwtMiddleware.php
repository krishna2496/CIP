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
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_TOKEN_NOT_PROVIDED'),
                trans('messages.custom_error_message.ERROR_TOKEN_NOT_PROVIDED')
            );
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_TOKEN_EXPIRED'),
                trans('messages.custom_error_message.ERROR_TOKEN_EXPIRED')
            );
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_IN_TOKEN_DECODE'),
                trans('messages.custom_error_message.ERROR_IN_TOKEN_DECODE')
            );
        } catch (\UnexpectedValueException $e) {
            return $this->responseHelper->error(
                Response::HTTP_BAD_REQUEST,
                Response::$statusTexts[Response::HTTP_BAD_REQUEST ],
                config('constants.error_codes.ERROR_IN_TOKEN_DECODE'),
                trans('messages.custom_error_message.ERROR_IN_TOKEN_DECODE')
            );
        }

        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
