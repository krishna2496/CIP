<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_skill';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_skill_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['user_skill_id', 'skill_id,', 'skill'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'skill_id'];

    /**
     * Store/update specified resource.
     *
     * @param  int  $userId
     * @param  int  $skillId
     * @return bool
     */
    public function linkUserSkill(int $userId, int $skillId): bool
    {
        return static::firstOrNew(array('user_id' => $userId, 'skill_id' => $skillId, 'deleted_at' => null))->save();
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $userId
     * @param  int  $skillId
     * @return  bool
     */
    public function deleteUserSkill(int $userId, int $skillId): bool
    {
        return static::where(['user_id' => $userId, 'skill_id' => $skillId])->forceDelete();
    }

    /**
     * Defined relation for the skill table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }

    /**
     * Find the specified resource.
     *
     * @param  int  $userId
     * @return Skill
     */
    public function find(int $userId): Skill
    {
        return static::with('skill')->find($userId);
    }

    /**
     * Delete user skills
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUserSkills(int $userId): bool
    {
        return static::where(['user_id' => $userId])->forceDelete();
    }
}
