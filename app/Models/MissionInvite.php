<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class MissionInvite extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_invite';

    /**
     * The primary key for the model.
     *
     * @var int
     */
    protected $primaryKey = 'mission_invite_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'from_user_id', 'to_user_id'];

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
     * Get mission invite record for a user
     * @var int $missionId
     * @var int $inviteUserId
     * @var int $fromUserId
     * @return Illuminate\Support\Collection
     */
    public function getMissionInvite(int $missionId, int $inviteUserId, int $fromUserId): Collection
    {
        return static::where(['mission_id' => $missionId,
        'to_user_id' => $inviteUserId, 'from_user_id' => $fromUserId])->get();
    }
}
