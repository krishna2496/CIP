<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavouriteMission extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'favourite_mission';

    /**
     * The primary key for the model.
     *
     * @var int
     */
    protected $primaryKey = 'favourite_mission_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'user_id'];
    
    /**
     * Get the mission that has media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
   
    /**
     * Store/update specified resource.
     *
     * @param  int  $userId
     * @param  int  $missionId
     * @return bool
     */
    public function addToFavourite(int $userId, int $missionId): bool
    {
        return static::withTrashed()->updateOrCreate(
            ['user_id' => $userId, 'mission_id' => $missionId]
        )->restore();
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $userId
     * @param  int  $missionId
     * @return bool
     */
    public function removeFromFavourite(int $userId, int $missionId): bool
    {
        return static::where(['user_id' => $userId, 'mission_id' => $missionId])->delete();
    }

    /**
     * Find specified resource.
     *
     * @param  int  $userId
     * @param  int  $missionId
     * @return array
     */
    public function findFavourite(int $userId, int $missionId)
    {
        return static::where('mission_id', $missionId)->where('user_id', $userId)->first();
    }
}
