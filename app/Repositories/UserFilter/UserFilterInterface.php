<?php
namespace App\Repositories\UserFilter;

use Illuminate\Http\Request;

interface UserFilterInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\UserFilter
     */
    public function userFilter(Request $request);

    /**
     * Store or Update created resource.
     *
     * @param array $request
     * @return App\Models\UserFilter
     */
    public function saveFilter(Request $request);
}
