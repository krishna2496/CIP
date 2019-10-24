<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class MissionApplication extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_application';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_application_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'user_id', 'applied_at', 'approval_status', 'motivation', 'availability_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_application_id', 'mission_id', 'user_id', 'applied_at', 'motivation',
    'availability_id', 'approval_status', 'user', 'first_name', 'last_name', 'avatar'];
  
    /**
     * Find listing of a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $missionId
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function find(Request $request, int $missionId): LengthAwarePaginator
    {
        $applicationQuery = $this->leftjoin('mission', 'mission.mission_id', '=', 'mission_application.mission_id');

        if ($request->has('search')) {
            $applicationQuery = $applicationQuery->where('motivation', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->has('status') && $request->input('status') !== '') {
            $applicationQuery = $applicationQuery->where('approval_status', strtoupper($request->status));
        }
        if ($request->has('user_id') && $request->input('user_id') !== '') {
            $applicationQuery = $applicationQuery->where('user_id', $request->user_id);
        }
        if ($request->has('type') && $request->input('type') !== '') {
            $applicationQuery = $applicationQuery->where('mission.mission_type', strtoupper($request->type));
        }
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $applicationQuery = $applicationQuery->orderBy('mission_application_id', $orderDirection);
        }

        $missionApplication = $applicationQuery->where('mission_application.mission_id', $missionId)
                ->paginate($request->perPage);
        return $missionApplication;
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $missionId
     * @param  int  $applicationId
     * @return array
     */
    public function findDetail(int $missionId, int $applicationId): array
    {
        $applicationQuery = $this;

        $missionApplication = $applicationQuery->where(
            ['mission_id' => $missionId, 'mission_application_id' => $applicationId]
        )->get()->toArray();
        return $missionApplication;
    }

    /**
     * Check already applied for a mission or not.
     *
     * @param int $missionId
     * @param int $userId
     * @return int
     */
    public function checkApplyMission(int $missionId, int $userId): int
    {
        return $this->where(['mission_id' => $missionId, 'user_id' => $userId])
        ->where('approval_status', '<>', config('constants.application_status.REFUSED'))
        ->count();
    }

    /**
     * Find listing of a resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $missionId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getVolunteers(Request $request, int $missionId): LengthAwarePaginator
    {
        $missionVolunteers = $this->select('user.user_id', 'user.first_name', 'user.last_name', 'user.avatar')
        ->where('mission_id', $missionId)
        ->where('approval_status', config("constants.application_status")["AUTOMATICALLY_APPROVED"])
        ->leftJoin('user', 'mission_application.user_id', '=', 'user.user_id')
        ->orderBy('mission_application.mission_application_id', 'desc')
        ->paginate($request->perPage);
        return $missionVolunteers;
    }
}
