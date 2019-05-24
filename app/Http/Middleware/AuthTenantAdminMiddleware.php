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

            $response['type'] = config('errors.type.ERROR_TYPE_422');
            $response['status'] = 402;
            $response['code'] = 10010;
            $response['message'] = config('errors.code.10010');
            $data["errors"][] = $response;

            return response()->json($data); 
        }
        try{
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
            $response['type'] = config('errors.type.ERROR_TYPE_403');
            $response['status'] = 403;
            $response['code'] = 10014;
            $response['message'] = config('errors.code.10014');
            $data["errors"][] = $response;

            return response()->json($data);

        } catch(\Exception $e){

            // That is database not found, that means there is DB_DATABASE constant in env file. That must need to remove from evn.
            if ($e->getCode()===1049) {
                
                $response['type'] = config('errors.type.ERROR_TYPE_400');
                $response['status'] = 400;
                $response['code'] = 10006;
                $response['message'] = config('errors.code.10006');
                $data["errors"][] = $response;

            }else{

                $response['type'] = config('errors.type.ERROR_TYPE_403');
                $response['status'] = 403;
                $response['code'] = 10014;
                $response['message'] = 'Unauthorised';
                $data["errors"][] = $response;
            }

            return response()->json($data);

        }
    }

    /**
     * Create new connection based on tenant_id
     *
     * @param  int $tenant_id     
     * @return integer/ Array
     */
    public function createConnection($tenant_id)
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

            $response['type'] = config('errors.type.ERROR_TYPE_400');
            $response['status'] = 400;
            $response['code'] = 10006;
            $response['message'] = config('errors.code.10006');
            $data["errors"][] = $response;

            return $data;

        }
    }
}
