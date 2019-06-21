<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use App\Helpers\{ResponseHelpers, DatabaseHelper};
use Closure, DB;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
                $domain = $credentials->fqdn;
            } catch (ExpiredException $e) {
                throw new ExpiredException();
            } catch (\Exception $e) {
                throw new \Exception();
            }
        } else {            
            // Uncomment below line while testing in apis with front side.
            // $domain = Helpers::getSubDomainFromRequest($request);
            
            // comment below line while testing in apis with front side.
            $domain = env('DEFAULT_TENANT');
        }

        if ($domain !== env('APP_DOMAIN')) {
            $tenant = DB::table('tenant')->select('tenant_id')->where('name', $domain)->whereNull('deleted_at')->first();
            if (!$tenant) {
                throw new ModelNotFoundException(trans('messages.custom_error_message.400000'));
            }
            DatabaseHelper::createConnection($tenant->tenant_id);
        }
        return $next($request);
    }
}
