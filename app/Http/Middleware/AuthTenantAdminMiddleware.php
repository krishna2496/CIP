<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use App\Helpers\{Helpers, ResponseHelper};
use DB, Closure;
use Illuminate\Http\Request;

class AuthTenantAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {        
        try{
			// Check basic auth passed or not
			if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])
				|| (empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']))
			) {
				return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_401'), 
											trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
											trans('messages.custom_error_code.ERROR_20010'), 
											trans('messages.custom_error_message.20010'));
			}
        
            // authenticate api user based on basic auth parameters
            $apiUser = DB::table('api_user')
						->where('api_key', base64_encode($_SERVER['PHP_AUTH_USER']))
						->where('api_secret', base64_encode($_SERVER['PHP_AUTH_PW']))
						->where('status','1')
						->whereNull('deleted_at')
						->first();
            
            // If user authenticates successfully
            if ($apiUser) {
                // Create connection with their tenant database
                $this->createConnection($apiUser->tenant_id);
                $response = $next($request);
                return $response;
            }
            // Send authentication error response if api user not found in master database
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_401'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_401'), 
                                        trans('messages.custom_error_code.ERROR_20008'), 
                                        trans('messages.custom_error_message.20008'));
        } catch(\Exception $e){
            // That is database not found, that means there is DB_DATABASE constant in env file. That must need to remove from env.
            if ($e->getCode() === 1049) {
                return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_TYPE_422'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_422'), 
                                        trans('messages.custom_error_code.ERROR_20016'), 
                                        trans('messages.custom_error_message.20016'));
            } else {
                return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_TYPE_403'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('messages.custom_error_code.ERROR_20014'), 
                                        trans('messages.custom_error_message.20014'));
            }
        }
    }

    /**
     * Create new connection based on tenant_id
     *
     * @param  int $tenant_id     
     * @return integer/ Array
     */
    public function createConnection(int $tenant_id)
    {        
        try{
            // Set configuration options for the newly create tenant
            Config::set('database.connections.tenant', array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenant_id,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            ));

            // Set default connection with newly created database
            DB::setDefaultConnection('tenant');
            DB::connection('tenant')->getPdo();

            return 1;

        } catch(\Exception $e){
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.custom_error_code.ERROR_21000'), 
                                        trans('api_error_messages.custom_error_message.21000'));

        }
    }
}
