<?php

namespace App\Repositories\MissionTheme;

use App\Repositories\MissionTheme\MissionThemeInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\Models\MissionTheme;

class MissionThemeRepository implements MissionThemeInterface
{
    public $theme;
	
	private $response;

    function __construct(MissionTheme $missionTheme, Response $response) {
		$this->missionTheme = $missionTheme;
		$this->response = $response;
    }		
	
	public function missionThemeList(Request $request) 
	{
		try {
			$themeQuery = $this->missionTheme->select('theme_name','mission_theme_id','translations')->get();
			return $themeQuery->toArray();
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

}