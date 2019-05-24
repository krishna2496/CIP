<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use Closure;
use Firebase\JWT\JWT;
use DB;

class TenantConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {        
        // Pre-Middleware Action
        $token = $request->get('token');

        if ($token) {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $domain = $credentials->fqdn;
        } else {
            $domain = $request->fqdn;
        }

        if ($domain !== env('APP_DOMAIN')) {
            
            $tenant = DB::table('tenant')->select('tenant_id')->where('name', $domain)->first();
            
            if (!$tenant) {
				$response['errors'] = array(array(
                    "type"=> config('errors.type.ERROR_TYPE_403'),
                    "status" => 403,
                    "code" => 40008,
                    "message"=> config('errors.code.40008')
                ));
                return response()->json($response, 403, [], JSON_NUMERIC_CHECK);
            }
            $this->createConnection($tenant);
        }        
        $response = $next($request);

        return $response;
    }
    /**
     * Create connection with specific tenant.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
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
                    "type"=> config('errors.type.ERROR_TYPE_403'),
                    "status" => 403,
                    "code" => 41000,
                    "message"=> config('errors.code.41000')
                ]);
                return response()->json($response);
            }
        }        
    }
}
