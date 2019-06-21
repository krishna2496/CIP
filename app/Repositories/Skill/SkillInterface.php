<?php
namespace App\Repositories\Skill;

use Illuminate\Http\Request;

interface SkillInterface {

	public function SkillList(Request $request);
}
