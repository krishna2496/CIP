<?php
namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\{Request, Response};
use App\Models\MissionTheme;

class MissionThemeRepository implements MissionThemeInterface
{
	/**
     * @var App\Models\MissionTheme
     */
    public $missionTheme;
	
	/**
     * @var Illuminate\Http\Response
     */
	private $response;

	/**
     * Create a new MissionTheme repository instance.
     *
     * @param  App\Models\MissionTheme $missionTheme
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    function __construct(MissionTheme $missionTheme, Response $response) {
		$this->missionTheme = $missionTheme;
		$this->response = $response;
    }		
	
	/**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
	public function missionThemeList(Request $request) 
	{
		return $this->missionTheme->select('theme_name','mission_theme_id','translations')->get();
	}

}