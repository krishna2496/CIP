<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use App\Helpers\Helpers;
use Closure;
use DB;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TenantConnectionMiddleware
{
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new middleware instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

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
            // $domain = $this->helpers->getSubDomainFromRequest($request);
            
            // comment below line while testing in apis with front side.
            $domain = env('DEFAULT_TENANT');
        }

        if ($domain !== env('APP_DOMAIN')) {
            $tenant = DB::table('tenant')->select('tenant_id')
            ->where('name', $domain)->whereNull('deleted_at')->first();
            if (!$tenant) {
                throw new ModelNotFoundException(trans('messages.custom_error_message.400000'));
            }
            $this->helpers->createConnection($tenant->tenant_id);
        }
        return $next($request);
    }
}
