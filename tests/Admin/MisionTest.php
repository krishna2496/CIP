<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;

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
                            "document_path" => "http://web8.anasource.com/team4/cip-api-swagger/pdf-test.pdf"
                        ]
                    ],
                    "media_images" => [[
                            "media_name" => "TatvaSoft-Software-Development-Company.png",
                            "media_type" => "png",
                            "media_path" => "http://web8.anasource.com/team4/cip-api-swagger/group-img1.png",
                            "default" => "1"
                        ]
                    ],
                    "media_videos" => [[
                        "media_name" => "youtube_small.mp4",
                        "media_type" => "mp4",
                        "media_path" => "https://www.youtube.com/watch?v=WfjO17X9hOo"
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

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
     * Show error for invalid data
     *
     * @return void
     */
    public function it_should_show_error_for_invalid_data_while_create_mission()
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

        $this->post("missions", $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
        Mission::orderBy("mission_id", "DESC")->take(1)->delete();
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
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $this->get(route('missions'), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        $params = [
                    "publication_status" => "DRAFT",
                ];

        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $mission_id = $mission->mission_id;

        $this->patch("missions/".$mission_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
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
        ->seeStatusCode(404);
    }
}
