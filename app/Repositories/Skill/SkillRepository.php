<?php

namespace App\Repositories\Skill;

use App\Repositories\Skill\SkillInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\Models\Skill;

class SkillRepository implements SkillInterface
{
    public $skill;
	
	private $response;

    function __construct(Skill $skill, Response $response) {
		$this->skill = $skill;
		$this->response = $response;
    }		
	
	public function SkillList(Request $request) 
	{
		try {
			$skillQuery = $this->skill->select('skill_name','skill_id','translations')->get();
			return $skillQuery->toArray();
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

    public function find(int $id) 
	{
		return $this->country->findSkill($id);
	}

}