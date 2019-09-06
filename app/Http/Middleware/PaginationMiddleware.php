<?php
namespace App\Http\Middleware;

use Closure;

class PaginationMiddleware
{
    private $perPageMax = config('constants.PER_PAGE_MAX');
   
    /**
     * Handle an incoming request.
     *
     * @param  object  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->perPage > $this->perPageMax || !is_numeric($request->perPage) && isset($request->perPage)) {
            $request->perPage = $this->perPageMax;
        }
        $request->merge(['perPage' => $request->get('perPage', config('constants.PER_PAGE_LIMIT'))]);
        return $next($request);
    }
}
