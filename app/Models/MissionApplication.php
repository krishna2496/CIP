<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected $visible = ['mission_application_id', 'mission_id',
    'user_id', 'applied_at', 'motivation', 'availability_id', 'approval_status'];

    /**
     * Defined relation for the mission table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }

    /**
     * Find listing of a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $missionId
     * @return array
     */
    public function find(Request $request, int $missionId)
    {
        $applicationQuery = $this;

        if ($request->has('search')) {
            $applicationQuery = $applicationQuery->where('motivation', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $applicationQuery = $applicationQuery->orderBy('mission_application_id', $orderDirection);
        }

        $missionApplication = $applicationQuery->where('mission_id', $missionId)
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
    public function findDetail(int $missionId, int $applicationId)
    {
        $applicationQuery = $this;
        $applicationQuery = $applicationQuery->orderBy('mission_application_id', 'asc');

        $missionApplication = $applicationQuery->where(
            ['mission_id' => $missionId, 'mission_application_id' => $applicationId]
        )
            ->get()->toArray();
        return $missionApplication;
    }

    /*
     * Check already applied for a mission or not.
     *
     * @param int $missionId
     * @param int $userId
     * @return int
     */
    public function checkApplyMission(int $missionId, int $userId): int
    {
        return $this->where(['mission_id' => $missionId, 'user_id' => $userId])
        ->count();
    }
}
