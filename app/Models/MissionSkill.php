<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Skill;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MissionSkill extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_skill';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_skill_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_skill_id', 'skill_id,', 'mission_id', 'skill','mission_count'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_skill_id','skill_id', 'mission_id'];

    /**
     * Get the skill associated with the mission skill.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skill(): HasOne
    {
        return $this->hasOne(Skill::class, 'skill_id', 'skill_id');
    }

    /**
     * Get the mission associated with the mission skill.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mission(): HasOne
    {
        return $this->hasOne(Mission::class, 'mission_id', 'mission_id');
    }
}
