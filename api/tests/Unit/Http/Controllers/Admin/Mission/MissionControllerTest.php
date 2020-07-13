<?php

namespace Tests\Unit\Http\Controllers\Admin\Mission;

use TestCase;
use Mockery;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Events\User\UserActivityLogEvent;
use App\Events\User\UserNotificationEvent;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Http\Controllers\Admin\Mission\MissionController;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use DB;

class MissionControllerTest extends TestCase
{
    /**
     * @testdox Test Error for amount is invalid
     *
     * @return void
     */
    public function testAmountInvalidForImpactDonationError()
    {
        \DB::setDefaultConnection('tenant');

        $this->assertTrue(true);
        $missionParam = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                'city_id' => 1,
                'country_code' => 'US'
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
            "mission_type" => "GOAL",
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => "APPROVED",
            "availability_id" => 1,
            "impact_donation" => [
                [
                    "amount" => 11.2,
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

        $missionRepositoryMockResponse = new Mission();
        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    "type"=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    "code"=> config('constants.error_codes.ERROR_INVALID_MISSION_DATA'),
                    "message"=> 'The impact donation amount must be an integer.'
                ]
            ]
        ];

        $JsonResponse = new JsonResponse(
            $methodResponse
        );

        $request = new Request($missionParam);
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $missionRepository = $this->mock(MissionRepository::class);
            
        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_REQUIRED_FIELDS_EMPTY'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            )
            ->andReturn($JsonResponse);

        // $notificationType = config('constants.notification_type_keys.NEW_MISSIONS');
        // $entityId = $missionRepositoryMockResponse->mission_id;
        // $action = config('constants.notification_actions.CREATED');
        // event(new UserNotificationEvent($notificationType, $entityId, $action));

        // event(new UserActivityLogEvent(
        //     config('constants.activity_log_types.MISSION'),
        //     config('constants.activity_log_actions.CREATED'),
        //     config('constants.activity_log_user_types.API'),
        //     $request->header('php-auth-user'),
        //     get_class($this),
        //     $request->toArray(),
        //     null,
        //     $missionRepositoryMockResponse->mission_id
        // ));

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository
        );

        $response = $callController->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }
    

    /**
     * @testdox Test store mission with impact donation attribute with success status
     *
     * @return void
     */
    public function testStoreImpactDonationAttributeSuccess()
    {
        \DB::setDefaultConnection('tenant');

        $this->assertTrue(true);
        $missionParam = [
            "organisation" => [
                "organisation_id" => 1,
                "organisation_name" => str_random(10),
                "organisation_detail" => ''
            ],
            "location" => [
                'city_id' => 1,
                'country_code' => 'US'
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
            "mission_type" => "GOAL",
            "goal_objective" => rand(1, 1000),
            "total_seats" => rand(10, 1000),
            "application_deadline" => "2022-07-28 11:40:00",
            "publication_status" => "APPROVED",
            "availability_id" => 1,
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

        $missionId = rand(1000, 9000);
        $missionRepositoryMockResponse = new Mission();
        $methodResponse = [
            "status"=> Response::HTTP_CREATED,
            "data"=> [
                "mission_id" => $missionId
            ],
            "message"=> trans('messages.success.MESSAGE_MISSION_ADDED')
        ];

        $JsonResponse = new JsonResponse(
            $methodResponse
        );

        $request = new Request($missionParam);
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);

        $missionRepository = $this->mock(MissionRepository::class);
        $missionRepository
            ->shouldReceive('store')
            ->once()
            ->with($request)
            ->andReturn($missionRepositoryMockResponse);
            
        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_CREATED,
                trans('messages.success.MESSAGE_MISSION_ADDED'),
                ['mission_id' => $missionId]
            )
            ->andReturn($JsonResponse);

        $notificationType = config('constants.notification_type_keys.NEW_MISSIONS');
        $entityId = $missionId;
        $action = config('constants.notification_actions.CREATED');
        // event(new UserNotificationEvent($notificationType, $entityId, $action));

        // event(new UserActivityLogEvent(
        //     config('constants.activity_log_types.MISSION'),
        //     config('constants.activity_log_actions.CREATED'),
        //     config('constants.activity_log_user_types.API'),
        //     $request->header('php-auth-user'),
        //     get_class($this),
        //     $request->toArray(),
        //     null,
        //     $missionId
        // ));

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository
        );

        $response = $callController->store($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
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
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
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
