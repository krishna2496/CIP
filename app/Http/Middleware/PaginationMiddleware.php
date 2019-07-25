<?php
namespace App\Http\Middleware;

use Closure;

class PaginationMiddleware
{
    private $perPageMax=10;
   
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->perPage > $this->perPageMax || !is_numeric($request->perPage) && isset($request->perPage)) {
            $request->perPage = $this->perPageMax;
        }
        $request->merge(['perPage' => $request->get('perPage', config('constants.PER_PAGE_LIMIT'))]);
        return $next($request);
    }
}
