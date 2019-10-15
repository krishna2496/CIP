<?php
use App\Helpers\Helpers;

class AppStoryTest extends TestCase
{
    /**
     * @test
     *
     * Add story
     *
     * @return void
     */
    public function it_should_add_story()
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

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if mission is invalid or deleted for add story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_for_add_story()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $params = [
            'mission_id' => rand(10000000, 50000000),
            'title' => str_random(10),
            'description' => str_random(50),
            'story_images[]' =>[]
        ];

        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if title invalid for add story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_title_for_add_story()
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
            'title' => "",
            'description' => str_random(50),
            'story_images[]' =>[]
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if image type is invalid for add story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_image_type_for_add_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $path  = storage_path().'/unitTestFiles/dummy.svg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'dummy.svg', '', null, null, true)
        );
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if video url is invalid for add story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_video_url_for_add_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if user add more than maximum video url's for add story
     *
     * @return void
     */
    public function it_should_return_error_for_maximum_video_url_limit_for_add_story()
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

        $storyVideos = '';
        $storyVideoUrl = 'https://www.youtube.com/watch?v=PCwL3-hkKrg,';
        for ($i=0; $i<=config("constants.STORY_MAX_VIDEO_LIMIT")+1 ; $i++) {
            $storyVideos .= $storyVideoUrl;
        }

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if user add more than maximum images for add story
     *
     * @return void
     */
    public function it_should_return_error_for_maximum_image_upload_limit_for_add_story()
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

        $path  = storage_path().'/unitTestFiles/test.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true)
        );

        for ($i=0; $i<=config("constants.STORY_MAX_IMAGE_LIMIT"); $i++) {
            array_push($storyImages, new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true));
        }

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
       
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error if user add more than 4mb image for add story
     *
     * @return void
     */
    public function it_should_return_error_for_image_size_limit_for_add_story()
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

        $path  = storage_path().'/unitTestFiles/SampleJPGImage_5mbmb.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'SampleJPGImage_5mbmb.jpg', '', null, null, true)
        );

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
       
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        $user->delete();
        $mission->delete();
    }
    
    /**
     * @test
     *
     * Return error if description is invalid for add story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_description_for_add_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50000),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Update story
     *
     * @return void
     */
    public function it_should_update_story()
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
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(200);        

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id for update story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_update_story()
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

        $params = [
            'mission_id' => rand(1000000, 5000000),
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();
        $this->patch('app/story/'.$story->story_id, $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid title for update story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_title_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => "",
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();
        $this->patch('app/story/'.$story->story_id, $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid image type for update story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_image_type_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');
        
        $path  = storage_path().'/unitTestFiles/dummy.svg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'dummy.svg', '', null, null, true)
        );
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid video url for update story
     *
     * @return void
     */
    public function it_should_return_error_invalid_video_url_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => str_random(10)
        ];
        DB::setDefaultConnection('mysql');
        
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error for maximum video url for update story
     *
     * @return void
     */
    public function it_should_return_error_for_maximum_video_url_limit_for_update_story()
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

        $storyVideos = '';
        $storyVideoUrl = 'https://www.youtube.com/watch?v=PCwL3-hkKrg,';
        for ($i=0; $i<=config("constants.STORY_MAX_VIDEO_LIMIT")+1 ; $i++) {
            $storyVideos .= $storyVideoUrl;
        }

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => $storyVideos
        ];
        DB::setDefaultConnection('mysql');
        
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }
    
    /**
     * @test
     *
     * It should return error for maximum image upload for update story
     *
     * @return void
     */
    public function it_should_return_error_for_maximum_image_upload_limit_for_update_story()
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

        for ($i=0; $i<=config("constants.STORY_MAX_IMAGE_LIMIT"); $i++) {
            array_push($storyImages, new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true));
        }

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');
        
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }
    
    /**
     * @test
     *
     * It should return error for maximum image upload size limit for update story
     *
     * @return void
     */
    public function it_should_return_error_for_maximum_image_upload_size_limit_for_update_story()
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

        $path  = storage_path().'/unitTestFiles/SampleJPGImage_5mbmb.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'SampleJPGImage_5mbmb.jpg', '', null, null, true)
        );

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50)
        ];
        DB::setDefaultConnection('mysql');
        
        $this->call('PATCH', 'app/story/'.$story->story_id, $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(422);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * Return error if description is invalid for update story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_description_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(201);
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();
        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50000),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->patch('app/story/'.$story->story_id, $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
        $mission->delete();
    }
    
    /**
     * @test
     *
     * Return error if story id is invalid for update story
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_story_id_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
               
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->patch('app/story/'.rand(1000000, 50000000), $params, ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
        $mission->delete();
    }
    
    /**
     * @test
     *
     * Return error if story is already published or declined for update story
     *
     * @return void
     */
    public function it_should_return_error_if_story_is_published_or_declined_for_update_story()
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

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('app/story', $params, ['token' => $token])
        ->seeStatusCode(201);
        $story = App\Models\Story::orderBy("story_id", "DESC")->take(1)->first();
        $story->update(['status' => config('constants.story_status.PUBLISHED')]);

        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(100),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        DB::setDefaultConnection('mysql');
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->patch('app/story/'.$story->story_id, $params, ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
        $mission->delete();
    }
    
    /**
     * @test
     *
     * It should do copy of declined story
     *
     * @return void
     */
    public function it_should_do_copy_of_declined_story()
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
        $story->update(['status' => config('constants.story_status.DECLINED')]);
        
        DB::setDefaultConnection('mysql');

        $this->get('app/story/'.$story->story_id.'/copy', ['token' => $token])
        ->seeStatusCode(200);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error if story is not declined on copy story
     *
     * @return void
     */
    public function it_should_return_error_if_story_is_not_declined_on_copy_story()
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

        $this->get('app/story/'.$story->story_id.'/copy', ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }
            
    /**
     * @test
     *
     * It should return error if story id not found on copy story
     *
     * @return void
     */
    public function it_should_return_error_if_story_id_not_found_on_copy_story()
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
        
        $this->get('app/story/'.rand(1000000, 5000000).'/copy', ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return no stories found on export story
     *
     * @return void
     */
    public function it_should_return_error_on_export_story()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('mysql');            
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $this->get('app/story/export', ['token' => $token])
        ->seeStatusCode(200);

        $user->delete();
    }
    
    /**
     * @test
     *
     * It should export story
     *
     * @return void
     */
    public function it_should_export_story()
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
            
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $params = [
            'mission_id' => $mission->mission_id,
            'title' => str_random(10),
            'description' => str_random(50),
            'story_videos' => 'https://www.youtube.com/watch?v=PCwL3-hkKrg,https://www.youtube.com/watch?v=PCwL3-hkKrg1'
        ];
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $path  = storage_path().'/unitTestFiles/test.jpg';
        $storyImages = array(
            new \Illuminate\Http\UploadedFile($path, 'test.jpg', '', null, null, true)
        );
        $this->call('POST', 'app/story', $params, [], ['story_images' => $storyImages], ['HTTP_token' => $token]);
        $this->seeStatusCode(201);

        DB::setDefaultConnection('mysql');

        $this->get('app/story/export', ['token' => $token])
        ->seeStatusCode(200);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
    }
    
    /**
     * @test
     *
     * It should submit story
     *
     * @return void
     */
    public function it_should_submit_story()
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

        $this->post('app/story/'.$story->story_id.'/submit', [], ['token' => $token])
        ->seeStatusCode(200);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error if story is published or declined on submit story
     *
     * @return void
     */
    public function it_should_return_error_on_submit_story()
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
        $story->update(['status' => config('constants.story_status.PUBLISHED')]);     
        DB::setDefaultConnection('mysql');

        $this->post('app/story/'.$story->story_id.'/submit', [], ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return error if story id not found on submit story
     *
     * @return void
     */
    public function it_should_return_error_if_story_id_not_found_on_submit_story()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));        
        $this->post('app/story/'.rand(1000000, 5000000).'/submit', [], ['token' => $token])                
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
    }

    /**
     * @test
     *
     * It should delete story image
     *
     * @return void
     */
    public function it_should_delete_story_image()
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
            'description' => str_random(50)
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
        $storyImage = App\Models\StoryMedia::where('story_id', $story->story_id)->take(1)->first();
   
        DB::setDefaultConnection('mysql');

        $this->delete('app/story/'.$story->story_id.'/image/'.$storyImage->story_media_id, [], ['token' => $token])
        ->seeStatusCode(204);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }
    
    /**
     * @test
     *
     * It should return error if data is invalid for delete story image
     *
     * @return void
     */
    public function it_should_return_error_if_data_is_invalid_for_delete_story_image()
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
        $storyImage = App\Models\StoryMedia::where('story_id', $story->story_id)->take(1)->first();
          
        DB::setDefaultConnection('mysql');

        // Return error if image id is invalid
        $this->delete('app/story/'.$story->story_id.'/image/'.rand(1000000, 5000000), [], ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        DB::setDefaultConnection('mysql');

        // Return error if story id is invalid
        $this->delete('app/story/'.rand(1000000, 5000000).'/image/'.$storyImage->story_media_id, [], ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $story->update(['status' => config('constants.story_status.PUBLISHED')]);     
        DB::setDefaultConnection('mysql');

        // Return error if story is already published or declined
        $this->delete('app/story/'.$story->story_id.'/image/'.$storyImage->story_media_id, [], ['token' => $token])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return all published story
     *
     * @return void
     */
    public function it_should_return_all_published_story_list()
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
        App\Models\Story::where('mission_id', '<>', $mission->mission_id)->delete();
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
        $story->update(['status' => config('constants.story_status.PUBLISHED')]);
        
        DB::setDefaultConnection('mysql');

        $this->get('app/story/list', ['token' => $token])
        ->seeStatusCode(200);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return return no records found on get published story list
     *
     * @return void
     */
    public function it_should_return_no_records_found_on_published_story_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::setDefaultConnection('tenant');

        App\Models\Story::where('deleted_at', '<>', null)->update(['status' =>config('constants.story_status.DRAFT')]);
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/story/list', ['token' => $token])
        ->seeStatusCode(200);

        $user->delete();
    }
    
    /**
     * @test
     *
     * It should return my story list
     *
     * @return void
     */
    public function it_should_return_my_story_list()
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
        App\Models\Story::where('mission_id', '<>', $mission->mission_id)->delete();
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
        $story->update(['status' => config('constants.story_status.PUBLISHED')]);
        
        DB::setDefaultConnection('mysql');

        $this->get('app/story/my-stories', ['token' => $token])
        ->seeStatusCode(200);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return return no records found on get my story list
     *
     * @return void
     */
    public function it_should_return_no_records_found_on_my_story_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        DB::setDefaultConnection('tenant');

        App\Models\Story::where('deleted_at', '<>', null)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        DB::setDefaultConnection('mysql');
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/story/my-stories', ['token' => $token])
        ->seeStatusCode(200);

        $user->delete();
    }
    
    /**
     * @test
     *
     * It should delete story
     *
     * @return void
     */
    public function it_should_delete_story()
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
        
        $this->delete('app/story/'.$story->story_id, [], ['token' => $token])
        ->seeStatusCode(204);

        DB::setDefaultConnection('mysql');

        // Return error if story id is invalid
        $this->delete('app/story/'.rand(1000000, 5000000), [], ['token' => $token])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return story details by story id
     *
     * @return void
     */
    public function it_should_return_detail_of_story()
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
        $this->get('app/story/'.$story->story_id, ['token' => $token])
        ->seeStatusCode(200);
        
        DB::setDefaultConnection('mysql');
        $story->update(['status' => config('constants.story_status.DECLINED')]);
        // If story is declined or published
        $this->get('app/story/'.$story->story_id, ['token' => $token])
        ->seeStatusCode(404);

        // If story id is not exist in system
        DB::setDefaultConnection('mysql');
        $this->get('app/story/'.rand(1000000, 50000000), ['token' => $token])
        ->seeStatusCode(404);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }

    /**
     * @test
     *
     * It should return story details by story id for edit story
     *
     * @return void
     */
    public function it_should_return_detail_for_edit_story()
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
        $this->get('app/edit/story/'.$story->story_id, ['token' => $token])
        ->seeStatusCode(200);
        
        DB::setDefaultConnection('mysql');
        $story->update(['status' => config('constants.story_status.PUBLISHED')]);
        // If story is declined or published
        $this->get('app/edit/story/'.$story->story_id, ['token' => $token])
        ->seeStatusCode(422);

        DB::setDefaultConnection('mysql');
        // If story id is not exist in system
        $this->get('app/edit/story/'.rand(1000000, 50000000), ['token' => $token])
        ->seeStatusCode(404);

        App\Models\Story::where('mission_id', $mission->mission_id)->delete();
        $user->delete();
        $mission->delete();
        $story->delete();
    }
}
