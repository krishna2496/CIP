<?php
namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MissionTheme;
use Illuminate\Support\Collection;

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
        return $this->missionTheme->select('theme_name', 'mission_theme_id', 'translations')->get();
    }
}
