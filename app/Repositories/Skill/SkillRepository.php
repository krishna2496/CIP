<?php
namespace App\Repositories\Skill;

use App\Repositories\Skill\SkillInterface;
use Illuminate\Http\Request;
use App\Models\Skill;

class SkillRepository implements SkillInterface
{
    /**
     * @var App\Models\Skill
     */
    public $skill;

    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Models\Skill $skill
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(Skill $skill)
    {
        $this->skill = $skill;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function skillList(Request $request)
    {
        return $this->skill->select('skill_name', 'skill_id', 'translations')->get();
    }
}
