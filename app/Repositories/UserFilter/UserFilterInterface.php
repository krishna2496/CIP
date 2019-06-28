<?php
namespace App\Repositories\UserFilter;

use Illuminate\Http\Request;

interface UserFilterInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function userFilter(Request $request);
}
