<?php
namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MissionTheme;

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
     * @return \Illuminate\Http\Response
     */
    public function missionThemeList(Request $request)
    {
        return $this->missionTheme->select('theme_name', 'mission_theme_id', 'translations')->get();
    }
}
