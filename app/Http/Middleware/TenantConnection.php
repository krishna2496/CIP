<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
// use App\Tenant;
use Closure;
use DB;

class TenantConnection
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
        // dd(DB::connection()->getDatabaseName());
        // Pre-Middleware Action
        $domain = $request->getHost();
        if ( $domain !== env('APP_DOMAIN') ) {

            $domain_array = explode(".",$domain);
            // $tenant = DB::table('tenants')->where('tenant_name',$domain_array[0])->first();
            $tenant = DB::table('tenants')->where('tenant_name',$request->get('tenant'))->first();
            // dd($tenant);
            if (!$tenant){
                // throw new SiteNotFoundException('Site with domain ' . $tenant . ' not found');
                $response['errors'] = [];
                array_push($response['errors'],[
                    "type"=> "Domain not found",
                    "status" => false,
                    "code" => 1001,
                    "message"=> "Domain '".$domain_array[0]."' not found"
                ]);
                // dd('Site with domain '.$tenant->tenant_name.'\'s database is not found.');
                return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
            }
            $this->createConnection($tenant);
        }

        $response = $next($request);

        return $response;
    }
    public function createConnection($tenant)
    {        
        // dd('ci_tenant_'.$tenant->id);
        Config::set('database.connections.tenant', array(
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'database'  => 'ci_tenant_'.$tenant->id,
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
        ));        
        try {
            // Create connection for the tenant database
            // dd(DB::connection()->getDatabaseName());
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
                    "message"=> "Domain '".$tenant->tenant_name."' have Unknown database connection request",
                ]);

                dd(response()->json($response),$e->getMessage());
            }
        }
        // dd(DB::connection()->getDatabaseName());
    }
}
