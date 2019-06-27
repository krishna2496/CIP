<?php
namespace App\Repositories\Skill;

use Illuminate\Http\Request;

interface SkillInterface
{
    public function skillList(Request $request);
}
