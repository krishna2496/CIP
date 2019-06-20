<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
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
     * Find the specified resource.
     *
     * @param  int  $user_id
     * @param  int  $skill_id
     * @return array
     */
    public function findUserSkill(int $user_id, int $skill_id)
    {
        return static::where(['user_id' => $user_id, 'skill_id' => $skill_id])->get();
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $user_id
     * @param  int  $skill_id
     * @return array
     */
    public function deleteUserSkill(int $user_id, int $skill_id)
    {
        return static::where(['user_id' => $user_id, 'skill_id' => $skill_id])->delete();
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
     * @return array
     */
    public function find(int $userId)
    {
        $skillQuery = static::with('skill');                     
        $userSkill = $skillQuery->where('user_id', $userId)
                ->paginate(config('constants.PER_PAGE_LIMIT'));
        return $userSkill;
    }

     
}
