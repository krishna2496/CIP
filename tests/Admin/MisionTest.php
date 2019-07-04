<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;

class MissionTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_mission()
    {
        $this->get(route('missions'), ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" => [
                [
                    "mission_id",
                    "theme_id",
                    "city_id",
                    "country_id",
                    "start_date",
                    "end_date",
                    "total_seats",
                    "mission_type",
                    "goal_objective",
                    "application_deadline",
                    "publication_status",
                    "organisation_id",
                    "organisation_name",
                    "mission_application_count",
                    "default_media_name",
                    "default_media_type",
                    "default_media_path",
                    "city_name",
                    "city" => [
                        "city_id",
                        "name"
                    ],
                    "mission_theme" => [
                        "mission_theme_id",
                        "theme_name",
                        "translations" => [
                            "*" => [
                                "lang",
                                "title"
                           ]
                        ]
                    ],
                    "mission_language" => [
                        [
                            "mission_language_id",
                            "language_id",
                            "title",
                            "short_description",
                            "description" => [
                                "*" => [
                                    "title",
                                    "description"
                                ]
                            ],
                            "objective",
                            "lang"
                        ]
                    ],
                    "mission_media" => [
                        "*" => [
                            "mission_media_id",
                            "media_name",
                            "media_type",
                            "media_path",
                            "default"
                        ]
                    ],
                    "mission_document" => [
                        "*" => [
                            "mission_document_id",
                            "document_name",
                            "document_type",
                            "document_path"
                        ]
                    ]
                ]
            ],
            "message"
        ]);
    }

    /**
     * @test
     *
     * No mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found()
    {
        $this->get(route("missions"), ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
    }
    
    /**
     * @test
     *
     * Create mission api
     *
     * @return void
     */
    public function it_should_create_mission()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => rand(1, 1),
                        "organisation_name" => str_random(10)
                    ],
                    "location" => [
                        "city_id" => rand(1, 1),
                        "country_code" => "IND"
                    ],
                    "mission_detail" => [[
                            "lang" => "en",
                            "title" => str_random(10),
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
                        ],
                        [
                            "lang" => "fr",
                            "title" => str_random(10),
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
                    "documents" => [
                        [
                            "document_name" => "pdf-test.pdf",
                            "document_type" => "pdf",
                            "document_path" => "http://www.orimi.com/pdf-test.pdf"
                        ]
                    ],
                    "media_images" => [[
                            "media_name" => "TatvaSoft-Software-Development-Company.png",
                            "media_type" => "png",
                            "media_path" => "https://www.tatvasoft.com/images/TatvaSoft-Software-Development-Company.png",
                            "default" => "1"
                        ]
                    ],
                    "media_videos" => [[
                        "media_name" => "youtube_small.mp4",
                        "media_type" => "mp4",
                        "media_path" => "http://techslides.com/demos/sample-videos/small.mp4"
                    ]],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2019-10-15 10:40:00",
                    "mission_type" => "GOAL",
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(1, 1000),
                    "application_deadline" => "2019-07-28 11:40:00",
                    "publication_status" => "DRAFT",
                    "theme_id" => rand(1, 1)
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'mission_id',
            ],
            'message',
            'status',
        ]);
        Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Show error for create mission api invalid data
     *
     * @return void
     */
    public function it_should_show_error_for_create_mission_invalid_data()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => rand(1, 1),
                        "organisation_name" => str_random(10)
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2019-10-15 10:40:00",
                    "mission_type" => "GOAL",
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(1, 1000),
                    "application_deadline" => "2019-07-28 11:40:00",
                    "publication_status" => "DRAFT",
                    "theme_id" => rand(1, 1)
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(422);
    }


    /**
     * @test
     *
     * Update mission api
     *
     * @return void
     */
    public function it_should_update_mission()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => rand(1, 1),
                        "organisation_name" => str_random(10)
                    ],
                    "location" => [
                        "city_id" => rand(1, 1),
                        "country_code" => "IND"
                    ],
                    "mission_detail" => [[
                            "lang" => "en",
                            "title" => str_random(10),
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
                        ],
                        [
                            "lang" => "fr",
                            "title" => str_random(10),
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
                    "documents" => [
                        [
                            "document_id" => "",
                            "document_name" => "pdf-test.pdf",
                            "document_type" => "pdf",
                            "document_path" => "http://www.orimi.com/pdf-test.pdf"
                        ]
                    ],
                    "media_images" => [
                        [
                            "media_id" => "",
                            "media_name" => "TatvaSoft-Software-Development-Company.png",
                            "media_type" => "png",
                            "media_path" => "https://www.tatvasoft.com/images/TatvaSoft-Software-Development-Company.png",
                            "default" => "1"
                        ]
                    ],
                    "media_videos" => [
                        [
                            "media_id" => "",
                            "media_name" => "youtube_small.mp4",
                            "media_type" => "mp4",
                            "media_path" => "http://techslides.com/demos/sample-videos/small.mp4"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2019-10-15 10:40:00",
                    "mission_type" => "GOAL",
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(1, 1000),
                    "application_deadline" => "2019-07-28 11:40:00",
                    "publication_status" => "DRAFT",
                    "theme_id" => rand(1, 1)
            ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $mission_id = $mission->mission_id;

        $this->patch("missions/".$mission_id, $params, ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
        $mission->delete();
    }

    /**
     * @test
     *
     * Update mission api with already deleted or not available mission id
     * @return void
     */
    public function it_should_return_mission_not_found_on_update()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => rand(1, 1),
                        "organisation_name" => str_random(10)
                    ],
                    "location" => [
                        "city_id" => rand(1, 1),
                        "country_code" => "IND"
                    ],
                    "mission_detail" => [[
                            "lang" => "en",
                            "title" => str_random(10),
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
                        ],
                        [
                            "lang" => "fr",
                            "title" => str_random(10),
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
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2019-10-15 10:40:00",
                    "mission_type" => "GOAL",
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(1, 1000),
                    "application_deadline" => "2019-07-28 11:40:00",
                    "publication_status" => "DRAFT",
                    "theme_id" => rand(1, 1)
            ];

        $this->patch(
            "missions/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
        )
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Delete mission
     *
     * @return void
     */
    public function it_should_delete_mission()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->delete(
            "missions/".$mission->mission_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
        )
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete mission api with already deleted or not available mission id
     * @return void
     */
    public function it_should_return_mission_not_found_on_delete()
    {
        $this->delete(
            "missions/".rand(1000000, 50000000),
            [],
            ['Authorization' => 'Basic '.base64_encode(env('DEFAULT_TENANT').'_api_key:'.env('DEFAULT_TENANT').'_api_secret')]
        )
        ->seeStatusCode(404);
    }
}
