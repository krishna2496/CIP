<?php

class MissionTest extends TestCase
{    
    /**
     * @test
     *
     * No mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found()
    {
        $this->get(route("missions"), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                            ],
                            "custom_information" => [
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
                    "media_images" => [[
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
                            "sort_order" => "1"
                        ],
                        [
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
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
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'mission_id',
            ],
            'message',
            'status',
        ]);
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Show error for invalid data
     *
     * @return void
     */
    public function it_should_show_error_for_invalid_data_while_create_mission()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Get all mission
     *
     * @return void
     */
    public function it_should_return_all_mission()
    {
        $description = str_random(20);
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
            ],
            "mission_detail" => [[
                    "lang" => "en",
                    "title" => str_random(10),
                    "short_description" => $description,
                    "objective" => str_random(20),
                    "custom_information" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ],
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
                    "short_description" => $description,
                    "objective" => str_random(20),
                    "custom_information" => [
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ],
                        [
                            "title" => str_random(10),
                            "description" => str_random(100),
                        ]
                    ],
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
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                    "sort_order" => "1"
                ]
            ],
            "media_videos"=> [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        DB::setDefaultConnection('mysql');
        $this->get('missions?order=desc&search='.$description, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data",
            "message"
        ]);
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
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
                    ],
                    "mission_detail" => [[
                            "lang" => "en",
                            "title" => str_random(10),
                            "short_description" => str_random(20),
                            "objective" => str_random(20),
                            "custom_information" => [
                                [
                                    "title" => str_random(10),
                                    "description" => str_random(100),
                                ],
                                [
                                    "title" => str_random(10),
                                    "description" => str_random(100),
                                ]
                            ],
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
                    "media_images" => [[
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
                            "sort_order" => "1"
                        ],
                        [
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_id" => "",
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_id" => "",
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.TIME"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1,
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->patch("missions/".$mission->mission_id, $params,
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
                "publication_status" => "DRAFT",
            ];

        $this->patch(
            "missions/".rand(1000000, 50000000),
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
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
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
    }

    /**
     * @test
     *
     * Create mission api return error If user enter goal mission type and do not enter goal objective
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_goal_objective()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => "",
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.DRAFT"),
                    "theme_id" => 1,
                    "availability_id" => 1
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Create mission api return error if user enter invalid mission type
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_type()
    {
        $params = [                    
                    "mission_type" => "GOAL1",                   
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    }

    /**
     * @test
     *
     * Get mission details by Id
     *
     * @return void
     */
    public function it_should_return_mission_detail_by_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->get("missions/".$mission->mission_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Get error for invalid mission id for get mission details by Id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id()
    {
        $missionId = rand(100000, 5000000);

        $this->get("missions/".$missionId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
    }

    /**
     * @test
     *
     * Validate data for update mission api
     *
     * @return void
     */
    public function it_should_validate_data_for_update_mission()
    {
        $params = [
                    "publication_status" => "test",
                ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->patch("missions/".$mission->mission_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $mission->delete();
    }

    /**
     * @test
     *
     * Return invalid argument for get all mission
     *
     * @return void
     */
    public function it_should_return_invalid_argument_for_get_all_mission()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->get('missions?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $mission->delete();
    }

    /**
     * @test
     *
     * Create missionand assign default media
     *
     * @return void
     */
    public function it_should_create_mission_and_assign_default_media()
    {
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                    "media_images" => [[
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'mission_id',
            ],
            'message',
            'status',
        ]);
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Update time mission
     *
     * @return void
     */
    public function it_should_update_mission_time_type()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                    "media_images" => [
                        [
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_id" => "",
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_id" => "",
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.TIME"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1,
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        DB::setDefaultConnection('mysql');

        $params = [
            "publication_status" => config("constants.publication_status.PUBLISHED_FOR_APPLYING"),
        ];

        $this->patch("missions/".$mission[0]['mission_id'], $params,
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Update goal mission
     *
     * @return void
     */
    public function it_should_update_mission_goal_type()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                    "media_images" => [
                        [
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_id" => "",
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_id" => "",
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1,
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];
        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        App\Models\MissionMedia::where("mission_id", $mission[0]['mission_id'])->delete();
        DB::setDefaultConnection('mysql');

        $this->patch("missions/".$mission[0]['mission_id'], $params,
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Update mission api
     *
     * @return void
     */
    public function it_should_update_time_mission()
    {
        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                "city_id" => 1,
                "country_code" => "US"
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
            "media_images" => [[
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                    "sort_order" => "1"
                ]
            ],
            "media_videos"=> [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "application_start_date" => "2019-05-15 10:40:00",
            "application_end_date" => "2020-05-15 10:40:00",
            "application_start_time" => "2019-05-15 10:40:00",
            "application_end_time" => "2020-05-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1
        ];

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);
        
        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->first();

        DB::setDefaultConnection('mysql');

        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        "city_id" => 1,
                        "country_code" => "US"
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
                    "media_images" => [[
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
                            "sort_order" => "1"
                        ],
                        [
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_id" => "",
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos"=> [[
                        "media_id" => "",
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.TIME"),
                    "goal_objective" => rand(1, 1000),
                    "total_seats" => rand(10, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "availability_id" => 1,
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $this->patch("missions/".$mission->mission_id, $params,
        ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
    }
}
