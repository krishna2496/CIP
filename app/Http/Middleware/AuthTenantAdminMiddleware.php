<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use Closure;
use DB;

class AuthTenantAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        // Check basic auth passed or not
        if(!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])){

            $response['errorType'] = config('errors.type.ERROR_TYPE_422');
            $response['apiStatus'] = 402;
            $response['apiErrorCode'] = 10010;
            $response['apiMessage'] = config('errors.code.10010');
            $data["errors"][] = $response;

            return response()->json($data); 
        }
        
        // authenticate api user based on basic auth parameters
        $apiUser = DB::table('api_user')
        ->where('api_key', base64_encode($_SERVER['PHP_AUTH_USER']))
        ->where('api_secret', base64_encode($_SERVER['PHP_AUTH_PW']))
        ->where('status','1')
        ->whereNull('deleted_at')
        ->first();

        // If user authenticate successfully
        if ($apiUser) {
            // Create connection with their tenant database
            $this->createConnection($apiUser->tenant_id);
            $response = $next($request);
            return $response;
        }

        // Send authentication error response if api user not found in master database
        $response['errorType'] = config('errors.type.ERROR_TYPE_403');
        $response['apiStatus'] = 403;
        $response['apiErrorCode'] = 10008;
        $response['apiMessage'] = 'Unauthorised';
        $data["errors"][] = $response;

        return response()->json($data);     
    }

    /**
     * Create new connection based on tenant_id
     *
     * @param  int $tenant_id     
     * @return void
     */
    public function createConnection($tenant_id)
    {        
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
    }
}
