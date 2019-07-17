<?php
namespace App\Repositories\Skill;

use App\Repositories\Skill\SkillInterface;
use Illuminate\Http\Request;
use App\Models\Skill;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @param string $skill_id
     * @return \Illuminate\Http\Response
     */
    public function skillList(Request $request, String $skill_id = '')
    {
        $skillQuery = $this->skill->select('skill_name', 'skill_id', 'translations');
        if ($skill_id != '') {
            $skillQuery->whereIn("skill_id", explode(",", $skill_id));
        }
        $skill = $skillQuery->get();
        return $skill;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function skillDetails(Request $request): LengthAwarePaginator
    {
        return $this->skill->select('skill_id', 'skill_name', 'translations', 'parent_skill')
        ->paginate($request->perPage);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\Skill
     */
    public function store(array $request): Skill
    {
        if ($request['parent_skill'] != 0) {
            $this->skill->findOrFail($request['parent_skill']);
        }
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
        if (isset($request['parent_skill'])) {
            if ($request['parent_skill'] != 0) {
                try {
                    $this->skill->findOrFail($request['parent_skill']);
                } catch (ModelNotFoundException $e) {
                    throw new ModelNotFoundException(
                        trans('messages.custom_error_message.ERROR_PARENT_SKILL_NOT_FOUND')
                    );
                }
            }
        }
        
        try {
            $skill = $this->skill->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND'));
        }
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
