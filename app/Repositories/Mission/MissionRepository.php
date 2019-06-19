<?php
namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use Illuminate\Http\{Request, Response};
use App\Helpers\{Helpers, ResponseHelper};
use App\Mission;
use App\Models\MissionApplication;
use Validator, PDOException, DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MissionRepository implements MissionInterface
{
    /**
     * @var App\Models\Mission
     */
    public $mission;

    /**
     * @var App\Models\MissionApplication
     */
    public $missionApplication;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Mission $mission
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(Mission $mission, MissionApplication $missionApplication, Response $response)
    {
        $this->mission = $mission;
        $this->response = $response;
        $this->missionApplication = $missionApplication;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {

    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
    }
    
    /**
     * Display a listing of all resources.
     *
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function missionApplicationList(Request $request)
    {
        try {
            $missionQuery = $this->user->with('mission');
            
            if ($request->has('search')) {
                $missionQuery->where(function ($query) use ($request) {
                    $query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
                    $query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
                });
            }
            if ($request->has('order')) {
                $orderDirection = $request->input('order', 'asc');
                $missionQuery->orderBy('user_id', $orderDirection);
            }
            
            $applicationList = $missionQuery->paginate(config('constants.PER_PAGE_LIMIT'));
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $applicationList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
       
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        
    }

}
