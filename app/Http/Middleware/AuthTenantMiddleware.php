<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use Closure;
use Firebase\JWT\JWT;
use DB;

class AuthTenantMiddleware
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
        // Pre-Middleware Action
        $token = $request->get('token');
        if($token){
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $domain = $credentials->fqdn;
        }else{            
            $domain = $request->fqdn;
        }        
        if ( $domain !== env('APP_DOMAIN') ) {
            
            $tenant_details = DB::table('tenant')->select('tenant_id')->where('name', $domain)->first();
            
            if (!$tenant_details){

                $response['errors'] = [];
                array_push($response['errors'],[
                    "type"=> "Domain not found",
                    "status" => false,
                    "code" => 10001,
                    "message"=> "Domain '".$domain."' not found"
                ]);                
                return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
            }
            $this->createConnection($tenant_details);
        }        
        $response = $next($request);

        return $response;
    }
    public function createConnection($tenant)
    {        
        Config::set('database.connections.tenant', array(
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'database'  => 'ci_tenant_'.$tenant->tenant_id,
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
        ));        
        try {
            // Create connection for the tenant database
            $pdo = DB::connection('tenant')->getPdo();
            // Set default database
            Config::set('database.default', 'tenant');
        } catch (\PDOException $e) {
            if ($e instanceof \PDOException) {
                $response['errors'] = [];
                array_push($response['errors'],[
                    "type"=> "Database Connection Error",
                    "status" => false,
                    "code" => 1000,
                    "message"=> "Domain '".$tenant->name."' have Unknown database connection request",
                ]);
                return response()->json($response);
            }
        }        
    }
}
