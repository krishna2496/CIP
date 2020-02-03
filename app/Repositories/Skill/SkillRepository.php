<?php
namespace App\Repositories\Skill;

use Illuminate\Http\Request;
use App\Models\Skill;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function skillList(Request $request, string $skill_id = '')
    {
        $skillQuery = $this->skill->select('skill_name', 'skill_id', 'translations');
        if ($skill_id !== '') {
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
        $skillQuery = $this->skill->select('skill_id', 'skill_name', 'translations', 'parent_skill');

        if ($request->has('id')) {
            $skillQuery = $skillQuery->whereIn('skill_id', $request->get('id'));
        }

        /*
         * Search on the internal name and the translations of a skill
         */
        if ($request->has('search')) {
            $searchString = $request->search;
            $skillQuery->where(function ($query) use ($searchString, $request) {
                $query->where('skill_name', 'like', '%' . $searchString . '%');
                // if the language is passed through the request, we can also search in the available translation for that language
                if ($request->has('searchLanguage')) {
                    $language = $request->searchLanguage;
                    /*
                     * Regex searches in the translation of the given language ($language) for the searchString
                     * ! Search in this won't work if the translation contains numbers or special characters
                     * "[[:space:]|[:alpha:]]{0,60}' . $searchString . '[[:space:]|[:alpha:]]{0,60}"
                     * means it only searches for letters and spaces before and after the $searchString
                     */
                    $query->orWhereRaw(
                        'translations regexp \'{s:4:"lang";s:2:"'
                            . $language
                            . '";s:5:"title";s:[0-9]{1,2}:"[[:space:]|[:alpha:]]{0,60}'
                            . $searchString
                            . '[[:space:]|[:alpha:]]{0,60}";}\''
                    );
                }
            });
        }

        /*
         * Filtering on translations
         * The regex here verifies that we have a translation (so no empty string)
         * for the given language codes passed in the key 'translations' of the $request
         */
        if ($request->has('translations')) {
            $availableTranslations = $request->translations;
            $skillQuery->where(function ($query) use ($availableTranslations, $request) {
                foreach ($availableTranslations as $languageCode) {
                    // Regex searches in translations column if the translation in the $languageCode exists and its length is greater than 0
                    $query->where('translations', 'regexp', '{s:4:"lang";s:2:"' . $languageCode . '";s:5:"title";s:[1-9][0-9]{0,1}:"');
                }
            });
        }

        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $skillQuery = $skillQuery->orderBy('skill_id', $orderDirection);
        }

        if ($request->has('limit') && $request->has('offset')) {
            $limit = $request->input('limit');
            $offset = $request->input('offset');
            $totalCount = $skillQuery->get()->count();
            $skills = $skillQuery->offset($offset)->limit($limit)->get();

            return new LengthAwarePaginator(
                $skills,
                $totalCount,
                $limit,
                $offset == 0 ? 1 : $offset
            );
        }

        return $skillQuery->paginate($request->perPage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\Skill
     */
    public function store(array $request): Skill
    {
        $request['parent_skill'] = $request['parent_skill'] ?? 0;
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
