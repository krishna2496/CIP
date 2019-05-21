<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\TenantMigrationJob;
use App\Tenant;
use Closure;
use DB;

class LoadSite
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
        $domain = $request->getHost();
        if ( $domain !== env('APP_DOMAIN') ) {

            $domain_array = explode(".",$domain);
            $tenant = Tenant::where('tenant_name',$domain_array[0])->first();

            if (!$tenant){
                // throw new SiteNotFoundException('Site with domain ' . $tenant . ' not found');
                dd('not found');
            }
            // dd($tenant);
            $this->loadSite($tenant);
        }

        $response = $next($request);

        return $response;
    }
    public function loadSite(Tenant $tenant)
    {
        Config::set('database.connections.tenant.host','127.0.0.1');
        Config::set('database.connections.tenant.username','root');
        Config::set('database.connections.tenant.password','');
        Config::set('database.connections.tenant.database',$tenant->tenant_name);

        //If you want to use query builder without having to specify the connection
        Config::set('database.default', 'tenant');        
        DB::purge('tenant');
        DB::reconnect('tenant');
        // dd(DB::connection('tenant'));        
        dispatch(new TenantMigrationJob);
        // Artisan::call('migrate --path=database/migrations/tenant');
        // dd(DB::connection()->getPdo());
    }
}
