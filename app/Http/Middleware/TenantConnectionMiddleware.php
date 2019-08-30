<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;
use App\Helpers\Helpers;
use Closure;
use DB;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\TenantDomainNotFoundException;

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
     * @param object $request
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
            try {
                $domain = $this->helpers->getSubDomainFromRequest($request);
            } catch (TenantDomainNotFoundException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new \Exception();
            }
        }
        $this->helpers->switchDatabaseConnection('mysql', $request);
        $tenant = DB::table('tenant')->select('tenant_id')
        ->where('name', $domain)->whereNull('deleted_at')->first();
        if (!$tenant) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.400000'));
        }
        $this->helpers->createConnection($tenant->tenant_id);
        return $next($request);
    }
}
