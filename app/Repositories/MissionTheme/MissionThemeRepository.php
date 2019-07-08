<?php
namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MissionTheme;
use Illuminate\Support\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

class MissionThemeRepository implements MissionThemeInterface
{
    /**
     * @var App\Models\MissionTheme
     */
    public $missionTheme;
 
    /**
     * Create a new MissionTheme repository instance.
     *
     * @param  App\Models\MissionTheme $missionTheme
     * @return void
     */
    public function __construct(MissionTheme $missionTheme)
    {
        $this->missionTheme = $missionTheme;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    public function missionThemeList(Request $request): Collection
    {
        return $this->missionTheme->select('mission_theme_id', 'theme_name', 'translations')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionThemeDetails(Request $request): LengthAwarePaginator
    {
        return $this->missionTheme->select('theme_name', 'mission_theme_id', 'translations')
        ->paginate(config('constants.PER_PAGE_LIMIT'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\MissionTheme
     */
    public function store(array $request): MissionTheme
    {
        return $this->missionTheme->create($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\Models\MissionTheme
     */
    public function update(array $request, int $id): MissionTheme
    {
        $missionTheme = $this->missionTheme->findOrFail($id);
        $missionTheme->update($request);
        return $missionTheme;
    }
    
    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\User
     */
    public function find(int $id): MissionTheme
    {
        return $this->missionTheme->findMissionTheme($id);
    }
    
    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->missionTheme->deleteMissionTheme($id);
    }
}
