<?php
use App\Helpers\Helpers;

class StoryTest extends TestCase
{
    /**
     * @test
     *
     * Fetch all user story
     *
     * @return void
     */
    public function it_should_fetch_all_user_story()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => [
                    [
                       "lang"=>"en",
                       "detail"=>"Testing organisation description in English"
                    ],
                    [
                       "lang"=>"fr",
                       "detail"=>"Testing organisation description in French"
                    ]
                ]
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => 'title',
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png",
                    "default" => "1"
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf"
                ]
            ],
            "media_videos"=> [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2019-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 1000),
            "application_deadline" => "2019-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1,
            "skills" => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ]
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->first();
        DB::setDefaultConnection('mysql');
    
        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $path  = storage_path().'/unitTestFiles/test.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true)
        );
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(201);

        DB::setDefaultConnection('mysql');

        $this->get('user/'.$user->user_id.'/stories', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);        
        App\Models\Story::where('mission_id', '<>', null)->delete();

        DB::setDefaultConnection('mysql');
        
        // If no data found for story
        $this->get('user/'.$user->user_id.'/stories', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);

        DB::setDefaultConnection('mysql');
        // If user is is invalid
        $this->get('user/'.rand(1000000, 5000000).'/stories', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(404);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Update user story status
     *
     * @return void
     */
    public function it_should_update_status_of_user_story()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => [
                    [
                       "lang"=>"en",
                       "detail"=>"Testing organisation description in English"
                    ],
                    [
                       "lang"=>"fr",
                       "detail"=>"Testing organisation description in French"
                    ]
                ]
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => 'title',
                    "short_description" => str_random(20),
                    "objective" => str_random(20),
                    "section" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ]
                ]
            ],
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png",
                    "default" => "1"
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf"
                ]
            ],
            "media_videos"=> [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2019-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(1, 1000),
            "application_deadline" => "2019-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1,
            "skills" => []
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->first();
        DB::setDefaultConnection('mysql');
    
        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $path  = storage_path().'/unitTestFiles/test.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true)
        );
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(201);
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();

        DB::setDefaultConnection('mysql');

        $params = ["status" => config('constants.story_status.DECLINED')];
        $this->patch('stories/'.$story->story_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]); 
        DB::setDefaultConnection('mysql');
        $params = ["status" => config('constants.story_status.PUBLISHED')];
        $this->patch('stories/'.$story->story_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);

        // If no data found for story
        DB::setDefaultConnection('mysql');
        $params = ["status" => 'test'];
        $this->patch('stories/'.$story->story_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        // If user is is invalid
        DB::setDefaultConnection('mysql');
        $params = ["status" => config('constants.story_status.DECLINED')];
        $this->patch('stories/'.rand(1000000, 5000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
        
        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
    }
}