<?php
use App\Helpers\Helpers;

class MissionTest extends TestCase
{
    /**
     * @test (priority=1)
     *
     * No mission found
     *
     * @return void
     */
    public function it_should_return_no_mission_found()
    {
        $this->get(route('missions'), ['Authorization' => Helpers::getBasicAuth()])
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "mission_tabs"=>[
                        [
                            "sort_key"=> 1,
                            "translations"=> [
                                [
                                    "lang"=> "en",
                                    "name"=> "Tab A1",
                                    "sections"=> [
                                        [
                                            "title"=> "First section",
                                            "content"=> str_random(100)
                                        ]
                                    ]
                                ],
                            ]
                        ],
                        [
                            "sort_key"=> 2,
                            "translations"=> [
                                [
                                    "lang"=> "en",
                                    "name"=> "Tab B1",
                                    "sections"=> [
                                        [
                                            "title"=> "B1 First section",
                                            "content"=> str_random(100)
                                        ]
                                    ]
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
                    "media_videos" => [[
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    "skills" => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        \DB::setDefaultConnection('mysql');

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ]
                ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

        $description = str_random(20);
        $params = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                'city_id' => $cityId,
                'country_code' => $countryDetail->ISO
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
            "mission_tabs"=>[
                [
                    "sort_key"=> 1,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> "Tab A1",
                            "sections"=> [
                                [
                                    "title"=> "First section",
                                    "content"=> str_random(100)
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    "sort_key"=> 2,
                    "translations"=> [
                        [
                            "lang"=> "en",
                            "name"=> "Tab B1",
                            "sections"=> [
                                [
                                    "title"=> "B1 First section",
                                    "content"=> str_random(100)
                                ]
                            ]
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
            "media_videos" => [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(10, 1000),
                "is_virtual" => 0
            ]
        ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        DB::setDefaultConnection('mysql');
        $this->get('missions?order=desc&search=' . $description, ['Authorization' => Helpers::getBasicAuth()])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data",
            "message"
          ]);

        DB::setDefaultConnection('mysql');
        $this->get('missions?perPage=all', ['Authorization' => Helpers::getBasicAuth()])
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "mission_tabs"=>[
                        [
                            "sort_key"=> 1,
                            "translations"=> [
                                [
                                    "lang"=> "en",
                                    "name"=> "Tab A1",
                                    "sections"=> [
                                        [
                                            "title"=> "First section",
                                            "content"=> str_random(100)
                                        ],
                                        [
                                            "title"=> "second section",
                                            "content"=> str_random(100)
                                        ],

                                    ]
                                ],
                            ]
                        ],
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
                    "media_videos" => [[
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
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

        $this->patch(
            'missions/' . $mission->mission_id,
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
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
            'missions/' . rand(1000000, 50000000),
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
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
            'missions/' . $mission->mission_id,
            [],
            ['Authorization' => Helpers::getBasicAuth()]
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
            'missions/' . rand(1000000, 50000000),
            [],
            ['Authorization' => Helpers::getBasicAuth()]
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.DRAFT"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ]
                ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('missions/' . $mission->mission_id, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('missions/' . $missionId, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->patch('missions/' . $mission->mission_id, $params, ['Authorization' => Helpers::getBasicAuth()])
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

        $this->get('missions?order=test', ['Authorization' => Helpers::getBasicAuth()])
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

        $params = [
                    "organisation" => [
                        "organisation_id" => 1,
                        "organisation_name" => str_random(10),
                        "organisation_detail" => ''
                    ],
                    "location" => [
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "media_videos" => [[
                        "media_name" => "youtube_small",
                        "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                        "sort_order" => "1"
                        ]
                    ],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ]
                ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "media_videos" => [[
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        DB::setDefaultConnection('mysql');

        $params = [
            "publication_status" => config("constants.publication_status.PUBLISHED_FOR_APPLYING"),
        ];

        $this->patch(
            'missions/' . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "media_videos" => [[
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];
        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        App\Models\MissionMedia::where("mission_id", $mission[0]['mission_id'])->delete();
        DB::setDefaultConnection('mysql');

        $this->patch(
            'missions/' . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
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
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
                'country_code' => $countryDetail->ISO
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
            "media_videos" => [[
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.TIME"),
            "goal_objective" => rand(1, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "application_start_date" => "2019-05-15 10:40:00",
            "application_end_date" => "2020-05-15 10:40:00",
            "application_start_time" => "2019-05-15 10:40:00",
            "application_end_time" => "2020-05-15 10:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(10, 1000),
                "is_virtual" => 0
            ]
        ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "media_videos" => [[
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $this->patch(
            'missions/' . $mission->mission_id,
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);
    }


    /**
     * @test
     *
     * Delete mission media
     *
     * @return void
     */
    public function it_should_delete_mission_media()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
                'country_code' => $countryDetail->ISO
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
                    "default" => "1",
                    "sort_order" => "1"
                ], [
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer6.png",
                    "default" => "0",
                    "sort_order" => "1"
                ]
            ],
            "documents" => [],
            "media_videos" => [],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(10, 1000),
                "is_virtual" => 0
            ],
            "skills" => []
        ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $missionId = json_decode($this->response->getContent())->data->mission_id;
        $missionMediaId = App\Models\MissionMedia::where(["mission_id" => $missionId, "default" => '0'])->first()->mission_media_id;
        App\Models\Mission::where("mission_id", "<>", $missionId)->delete();

        DB::setDefaultConnection('mysql');
        $this->delete('missions/media/' . $missionMediaId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);

        App\Models\Mission::where("mission_id", $missionId)->delete();
    }

    /**
     * @test
     *
     * Delete mission document
     *
     * @return void
     */
    public function it_should_delete_mission_document()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                'city_id' => $cityId,
                'country_code' => $countryDetail->ISO
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
                    "default" => "1",
                    "sort_order" => "1"
                ]
            ],
            "documents" => [[
                    "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                    "sort_order" => "1"
                ]
            ],
            "media_videos" => [],
            "start_date" => "2019-05-15 10:40:00",
            "end_date" => "2022-10-15 10:40:00",
            "mission_type" => config("constants.mission_type.GOAL"),
            "goal_objective" => rand(1, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "volunteering_attribute" => [
                "availability_id" => 1,
                "total_seats" => rand(10, 1000),
                "is_virtual" => 0
            ],
            "skills" => []
        ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $missionId = json_decode($this->response->getContent())->data->mission_id;
        $missionDocumentId = App\Models\MissionDocument::where("mission_id", $missionId)->first()->mission_document_id;
        App\Models\Mission::where("mission_id", "<>", $missionId)->delete();

        DB::setDefaultConnection('mysql');
        $this->delete('missions/document/' . $missionDocumentId, [], ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(204);

        DB::setDefaultConnection('mysql');
        // Return error if document not found in system
        $this->delete('missions/document/' . $missionDocumentId, [], ['Authorization' => Helpers::getBasicAuth()])
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
        App\Models\Mission::where("mission_id", $missionId)->delete();
    }

    /**
     * @test
     *
     * Create mission api
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_document_path_for_create_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                        ]
                    ],
                    "media_images" => [[
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_path" => "test",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos" => [],
                    "start_date" => "2019-05-15 10:40:00",
                    "end_date" => "2022-10-15 10:40:00",
                    "mission_type" => config("constants.mission_type.GOAL"),
                    "goal_objective" => rand(1, 1000),
                    "application_deadline" => "2022-07-28 11:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    "skills" => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        \DB::setDefaultConnection('mysql');
        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Update mission and valid document, video and images before update
     *
     * @return void
     */
    public function it_should_return_error_if_media_is_not_linked_to_mission_on_update_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "media_videos" => [[
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
                    "application_deadline" => "2022-07-28 11:40:00",
                    "application_start_date" => "2019-05-15 10:40:00",
                    "application_end_date" => "2020-05-15 10:40:00",
                    "application_start_time" => "2019-05-15 10:40:00",
                    "application_end_time" => "2020-05-15 10:40:00",
                    "publication_status" => config("constants.publication_status.APPROVED"),
                    "theme_id" => 1,
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
                    'skills' => [
                        [
                            "skill_id" => $skill->skill_id
                        ]
                    ]
                ];

        $this->post('missions', $params, ['Authorization' => Helpers::getBasicAuth()])
        ->seeStatusCode(201);

        $mission = App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->get();
        DB::setDefaultConnection('mysql');

        $this->patch(
            'missions/' . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status',
            ]);

        DB::setDefaultConnection('mysql');

        // Validate media id
        $params = [
            "media_images" => [
                [
                    "media_id" => rand(10000000, 50000000),
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "",
                    "sort_order" => "1"
                ]
            ]
        ];

        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(404);

        DB::setDefaultConnection('mysql');

        // Validate video id
        $params = [
            "media_videos" => [[
                "media_id" => rand(10000000, 50000000),
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ]
        ];

        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(404);

        DB::setDefaultConnection('mysql');

        // Validate document id
        $params = [
            "documents" => [[
                "document_id" => rand(10000000, 50000000),
                "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                "sort_order" => "1"
            ]
            ]
        ];

        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(404);

        DB::setDefaultConnection('tenant');
        // Validate media id
        $params = [
            "media_images" => [
                [
                    "media_id" => App\Models\MissionMedia::where('mission_id', "<>", $mission[0]['mission_id'])->first()->mission_media_id,
                    "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                    "default" => "",
                    "sort_order" => "1"
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(422);

        DB::setDefaultConnection('tenant');

        // Validate media id
        $params = [
            "media_videos" => [[
                "media_id" => App\Models\MissionMedia::where('mission_id', "<>", $mission[0]['mission_id'])->first()->mission_media_id,
                "media_name" => "youtube_small",
                "media_path" => "https://www.youtube.com/watch?v=PCwL3-hkKrg",
                "sort_order" => "1"
                ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(422);

        DB::setDefaultConnection('tenant');
        // Validate document id
        $params = [
            "documents" => [[
                "document_id" => App\Models\MissionDocument::where('mission_id', "<>", $mission[0]['mission_id'])->first()->mission_document_id,
                "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                "sort_order" => "1"
            ]
            ]
        ];

        DB::setDefaultConnection('mysql');
        $this->patch(
            "missions/" . $mission[0]['mission_id'],
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(422);

        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
    }

    /**
     * @test
     *
     * Update mission api
     *
     * @return void
     */
    public function it_should_return_error_on_update_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
                    ],
                    "mission_detail" => [[
                            "lang" => "en",
                            "title" => str_random(10),
                            "short_description" => str_random(20),
                            "objective" => str_random(20),
                            "section" => [
                            ]
                        ]
                    ],
                    "media_images" => [[
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
                            "sort_order" => "1"
                        ]
                    ],
                    "documents" => [[
                            "document_id" => "",
                            "document_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/test/sample.pdf",
                            "sort_order" => "1"
                        ]
                    ],
                    "media_videos" => [[
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

        $this->patch(
            "missions/" . $mission->mission_id,
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(422);
        $mission->delete();    }

    /**
     * @test
     *
     * Invalid mission tab id for update api
     *
     * @return void
     */
    public function it_should_return_error_on_invalid_mission_tab_id()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "mission_tabs"=>[
                        [
                            'mission_tab_id'=>str_random(36),
                            "sort_key"=> 1,
                            "translations"=> [
                                [
                                    "lang"=> "en",
                                    "name"=> "Tab A1",
                                    "sections"=> [
                                        [
                                            "title"=> "First section",
                                            "content"=> str_random(100)
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ],
                    "media_images" => [[
                            "media_id" => "",
                            "media_path" => "https://optimy-dev-tatvasoft.s3.eu-central-1.amazonaws.com/default_theme/assets/images/volunteer9.png",
                            "default" => "1",
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
                    "volunteering_attribute" => [
                        "availability_id" => 1,
                        "total_seats" => rand(10, 1000),
                        "is_virtual" => 0
                    ],
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

        $this->patch(
            "missions/".$mission->mission_id,
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
        $mission->delete();
    }


    /**
     * @test
     *
     * Update mission tab with mission tab id api
     *
     * @return void
     */
    public function it_should_update_mission_tab_with_mission_tab_id()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;

        \DB::setDefaultConnection('mysql');

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
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
                    "mission_tabs"=>[
                        [
                            "sort_key"=> 1,
                            "translations"=> [
                                [
                                    "lang"=> "en",
                                    "name"=> "Tab A1",
                                    "sections"=> [
                                        [
                                            "title"=> "First section",
                                            "content"=> str_random(100)
                                        ],
                                        [
                                            "title"=> "second section",
                                            "content"=> str_random(100)
                                        ],

                                    ]
                                ],
                            ]
                        ],
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

        $this->patch(
            "missions/".$mission->mission_id,
            $params,
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
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
     * Update organization while create mission api
     *
     * @return void
     */
    public function it_should_update_organization_while_create_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        
        \DB::setDefaultConnection('mysql');

        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();

        $organization = factory(\App\Models\Organization::class)->make();
        $organization->setConnection($connection);
        $organization->save();
        $organizationId = $organization->organization_id;
 
        $params = [
                    "organization" => [
                        "organisation_detail" => '',
                        "organization_id" => $organizationId,
                        "name"=> str_random(10),
                        "legal_number"=> "12345678934",
                        "phone_number"=> "1231231231",
                        "address_line_2"=> "string",
                        "address_line_1"=> "string",
                        "city_id"=> $cityId,
                        "state_id"=> "1",
                        "country_id"=> $countryDetail->country_id,
                        "postal_code"=> "string"
                    ],
                    "location" => [
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
        
        \DB::setDefaultConnection('mysql');
        
        $this->post(
            "missions",
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'data' => [
                'mission_id',
            ],
            'message',
            'status',
        ]);
        App\Models\Mission::orderBy("mission_id", "DESC")->take(1)->delete();
        App\Models\Organization::whereNull('deleted_at')->where('organization_id', $organizationId)->delete();
    }

    /**
    * @test
    *
    * Create organization while create mission api
    *
    * @return void
    */
    public function it_should_create_organization_while_create_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        
        \DB::setDefaultConnection('mysql');

        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organization" => [
                        "organisation_detail" => '',
                        "name"=> str_random(10),
                        "legal_number"=> "12345678934",
                        "phone_number"=> "1231231231",
                        "address_line_2"=> "string",
                        "address_line_1"=> "string",
                        "city_id"=> $cityId,
                        "state_id"=> "1",
                        "country_id"=> $countryDetail->country_id,
                        "postal_code"=> "string"
                    ],
                    "location" => [
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
        
        \DB::setDefaultConnection('mysql');
        
        $this->post(
            "missions",
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
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
     * Organization not found while create mission api
     *
     * @return void
     */
    public function it_should_through_organization_not_found_while_create_mission()
    {
        \DB::setDefaultConnection('tenant');
        $countryDetail = App\Models\Country::with('city')->whereNull('deleted_at')->first();
        $cityId = $countryDetail->city->first()->city_id;
        
        \DB::setDefaultConnection('mysql');

        $connection = 'tenant';
        $skill = factory(\App\Models\Skill::class)->make();
        $skill->setConnection($connection);
        $skill->save();
 
        $params = [
                    "organization" => [
                        "organisation_detail" => '',
                        "organization_id" => str_random(10),
                        "name"=> str_random(10),
                        "legal_number"=> "12345678934",
                        "phone_number"=> "1231231231",
                        "address_line_2"=> "string",
                        "address_line_1"=> "string",
                        "city_id"=> $cityId,
                        "state_id"=> "1",
                        "country_id"=> $countryDetail->country_id,
                        "postal_code"=> "string"
                    ],
                    "location" => [
                        'city_id' => $cityId,
                        'country_code' => $countryDetail->ISO
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
        
        \DB::setDefaultConnection('mysql');
        
        $this->post(
            "missions",
            $params,
            ['Authorization' => Helpers::getBasicAuth()]
        )
        ->seeStatusCode(422);
    }
}
