<?php
namespace App\Repositories\UserFilter;

use App\Repositories\UserFilter\UserFilterInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UserFilter;
use App\Traits\RestExceptionHandlerTrait;

class UserFilterRepository implements UserFilterInterface
{
    use RestExceptionHandlerTrait;
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
     * @param Illuminate\Http\Request $request
     * @return App\Models\UserFilter
     */
    public function userFilter(Request $request): UserFilter
    {
        return $this->filters->get()->where("user_id", $request->auth->user_id)->first();
    }

    /**
     * Store or Update created resource.
     *
     * @param  Illuminate\Http\Request
     * @return App\Models\UserFilter
     */
    public function saveFilter(Request $request): UserFilter
    {
        // Save user filter data to database
        $userFilterData["search"] = $request->has('search') ? $request->input('search') : '';
        $userFilterData["country_id"] = $request->has('country_id') ? $request->input('country_id') : '';
        $userFilterData["city_id"] = $request->has('city_id') ? $request->input('city_id') : '';
        $userFilterData["theme_id"] = $request->has('theme_id') ? $request->input('theme_id') : '';
        $userFilterData["skill_id"] = $request->has('skill_id') ? $request->input('skill_id') : '';
        $userFilter= $this->filters->createOrUpdateUserFilter(
            ['user_id' => $request->auth->user_id],
            array('filters' => $userFilterData)
        );

        return $userFilter;
    }
}
