<?php

namespace App\Repositories\Skill;

use Illuminate\Http\Request;

interface SkillInterface {

	// public function save(array $data);	
	public function SkillList(Request $request);
}
