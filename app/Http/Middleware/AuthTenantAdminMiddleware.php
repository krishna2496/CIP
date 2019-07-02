<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use DB;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDOException;
use App\Traits\RestExceptionHandlerTrait;

class AuthTenantAdminMiddleware
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new middleware instance.
     *
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
        $this->helpers = new Helpers;
    }
 
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check basic auth passed or not
            if ($request->header('php-auth-user') == null && $request->header('php-auth-pw') == null
                || (empty($request->header('php-auth-user')) && empty($request->header('php-auth-pw')))
            ) {
                return $this->responseHelper->error(
                    Response::HTTP_UNAUTHORIZED,
                    Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                    config('constants.error_codes.ERROR_API_AND_SECRET_KEY_REQUIRED'),
                    trans('messages.custom_error_message.ERROR_API_AND_SECRET_KEY_REQUIRED')
                );
            }
            // authenticate api user based on basic auth parameters
            $apiUser = DB::table('api_user')
                        ->where('api_key', base64_encode($request->header('php-auth-user')))
                        ->where('api_secret', base64_encode($request->header('php-auth-pw')))
                        ->where('status', '1')
                        ->whereNull('deleted_at')
                        ->first();
            
            // If user authenticates successfully
            if ($apiUser) {
                // Create connection with their tenant database
                $this->helpers->createConnection($apiUser->tenant_id);
                return $next($request);
            }
            // Send authentication error response if api user not found in master database
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                config('constants.error_codes.ERROR_INVALID_API_AND_SECRET_KEY'),
                trans('messages.custom_error_message.ERROR_INVALID_API_AND_SECRET_KEY')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
