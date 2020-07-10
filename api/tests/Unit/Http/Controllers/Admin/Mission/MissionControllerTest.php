<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use  App\Repositories\Mission\MissionRepository;
use  App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Helpers\LanguageHelper;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Http\Controllers\Admin\MissionController;

class MissionControllerTest extends TestCase
{
    /**
     * A store method example.
     *
     * @return void
     */
    public function testStoreImpactDonationAttribute()
    {
        $this->assertTrue(true);
        $request = new Request();
        // $missionData = factory(App\Models\Mission::class)->make();
        $missionParam = [
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
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => config("constants.publication_status.APPROVED"),
            "theme_id" => 1,
            "availability_id" => 1,
            "skills" => [
                [
                    "skill_id" => $skill->skill_id
                ]
            ],
            "impact_donation" => [
                [
                    "amount" => 1,
                    "translations" => [
                        [
                            "language_code" => "en",
                            "content" => "this is test impact donation mission in english language."
                        ],
                        [
                            "language_code" => "es",
                            "content" => "this is test impact donation mission in spanish language."
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
    * Create a new service instance.
    *
    * @param  App\Services\UserService $userService
    *
    * @return void
    */
    private function getController(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        Request $request,
        LanguageHelper $languageHelper,
        MissionMediaRepository $missionMediaRepository,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        NotificationRepository $notificationRepository
    ) {
        return new MissionController(
            $userRepository,
            $responseHelper,
            $languageHelper,
            $userService,
            $timesheetService,
            $helpers,
            $request,
            $notificationRepository
        );
    }

    /**
    * Mock an object
    *
    * @param string name
    *
    * @return Mockery
    */
    private function mock($class)
    {
        return Mockery::mock($class);
    }
}
