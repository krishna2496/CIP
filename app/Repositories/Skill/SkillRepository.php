<?php
namespace App\Repositories\Skill;

use App\Repositories\Skill\SkillInterface;
use Illuminate\Http\Request;
use App\Models\Skill;
use Illuminate\Pagination\LengthAwarePaginator;

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
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function skillDetails(Request $request): LengthAwarePaginator
    {
        return $this->skill->select('skill_id', 'skill_name', 'translations')
        ->paginate(config('constants.PER_PAGE_LIMIT'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\Skill
     */
    public function store(array $request): Skill
    {
        return $this->skill->create($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\Models\Skill
     */
    public function update(array $request, int $id): Skill
    {
        $skill = $this->skill->findOrFail($id);
        $skill->update($request);
        return $skill;
    }
    
    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\Models\Skill
     */
    public function find(int $id): Skill
    {
        return $this->skill->findSkill($id);
    }
    
    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->skill->deleteSkill($id);
    }
}
