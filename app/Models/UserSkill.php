<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

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
    protected $visible = ['user_skill_id', 'user_id', 'skill_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'skill_id'];

    /**
     * The rules that should validate reset password request.
     *
     * @var array
     */
    public $rules = [
        'user_id' => 'required',
        'skills' => 'required',
        'skills.*.skill_id' => 'required|string',
    ];

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
}
