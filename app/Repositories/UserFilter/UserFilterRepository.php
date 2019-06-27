<?php
namespace App\Repositories\UserFilter;

use App\Repositories\UserFilter\UserFilterInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UserFilter;

class UserFilterRepository implements UserFilterInterface
{
    /**
     * @var App\Models\UserFilter
     */
    public $filters;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new User repository instance.
     *
     * @param  App\Models\UserFilter $UserFilter
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(UserFilter $filters, Response $response)
    {
        $this->filters = $filters;
        $this->response = $response;
    }

    /**
     * Display a listing of User filter.
     *
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function userFilter(Request $request)
    {
        return $this->filters->get()->where("user_id", $request->auth->user_id)->first();
    }
}
