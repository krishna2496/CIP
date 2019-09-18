<?php

class ThemeTest extends TestCase
{
    /**
     * @test
     *
     * Create theme
     *
     * @return void
     */
    public function it_should_create_theme()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if user do not enter theme name
     *
     * @return void
     */
    public function it_should_return_error_if_theme_name_is_blank()
    {
        $params = [        
            "theme_name" => "",
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Get all themes
     *
     * @return void
     */
    public function it_should_return_all_themes_for_admin()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        DB::setDefaultConnection('mysql');

        $this->get('entities/themes', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Get a theme by theme id
     *
     * @return void
     */
    public function it_should_return_a_theme_for_admin_by_mission_theme_id()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        $theme = App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->get();
        $themeId = $theme[0]->mission_theme_id;
        DB::setDefaultConnection('mysql');

        $this->get('entities/themes/'.$themeId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                "mission_theme_id",
                "theme_name",
                "translations"
            ],
            "message"
        ]);
        App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Return error if theme id is wrong
     *
     * @return void
     */
    public function it_should_return_error_if_mission_theme_id_is_wrong()
    {
        $this->get('entities/themes/'.rand(1000000,2000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * It should update theme
     *
     * @return void
     */
    public function it_should_update_theme()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $theme = App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->get();
        $themeId = $theme[0]->mission_theme_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "theme_name" => str_random(20)
        ];
        
        $this->patch('entities/themes/'.$themeId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\MissionTheme::where("mission_theme_id", $themeId)->delete();
    }

    /**
     * @test
     *
     * It should return error for blank theme name
     *
     * @return void
     */
    public function it_should_return_error_for_update_theme_blank_theme_name()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        
        $theme = App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->get();
        $themeId = $theme[0]->mission_theme_id;
        DB::setDefaultConnection('mysql');

        $params = [        
            "theme_name" => ""
        ];
        
        $this->patch('entities/themes/'.$themeId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        App\Models\MissionTheme::where("mission_theme_id", $themeId)->delete();
    }

    /**
     * @test
     *
     * It should return error if user enter wrong theme id
     *
     * @return void
     */
    public function it_should_return_error_for_wrong_mission_theme_id()
    {   
        $params = [        
            "theme_name" => str_random(20),
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->patch('entities/themes/'.rand(1000000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * It should delete theme
     *
     * @return void
     */
    public function it_should_delete_theme()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);

        $theme = App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->get();
        $themeId = $theme[0]->mission_theme_id;
        DB::setDefaultConnection('mysql');
        
        $this->delete('entities/themes/'.$themeId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * It should return error for invalid theme id for delete theme
     *
     * @return void
     */
    public function it_should_return_error_for_delete_theme_for_invalid_mission_theme_id()
    {   
        $this->delete('entities/themes/'.rand(1000000, 5000000), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Return invalid argument error
     *
     * @return void
     */
    public function it_should_return_invalid_argument_error_for_get_all_themes_for_admin()
    {
        $themeName = str_random(20);
        $params = [        
            "theme_name" => $themeName,
            "translations" => [
                [
                    "lang" => "en",
                    "title" => "theme testing"
                ]
            ]
        ];

        $this->post("entities/themes", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]);
        DB::setDefaultConnection('mysql');

        $this->get('entities/themes?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        App\Models\MissionTheme::where("theme_name", $themeName)->orderBy("mission_theme_id", "DESC")->take(1)->delete();
    }
}
