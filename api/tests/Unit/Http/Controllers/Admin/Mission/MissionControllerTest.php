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
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;
use App\Services\Mission\ModelsService;
use App\Models\TimeMission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Models\FavouriteMission;
use App\Models\MissionSkill;
use App\Models\MissionRating;
use App\Models\MissionApplication;
use App\Models\City;
use App\Models\MissionTab;
use App\Models\MissionTabLanguage;
use App\Repositories\Organization\OrganizationRepository;
use Validator;

class MissionControllerTest extends TestCase
{
    /**
     * @testdox Test udpate mission with impact donation attribute with success status
     */
    public function testUpdateImpactDonationAttributeSuccess()
    {
        \DB::setDefaultConnection('tenant');

        $data = [
            'impact_donation' => [
                [
                    'impact_donation_id' => str_random(36),
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'es',
                            'content' => str_random(160)
                        ]
                    ]
                ],
                [
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'ab',
                            'content' => str_random(160)
                        ]
                    ]
                ]
            ],
            'publication_status' => 'DRAFT'
        ];

        $requestData = new Request($data);
        $missionId = rand(50000, 70000);

        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = $this->mock(Request::class);
        $mission = $this->mock(Mission::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);
        $modelService = $this->mock(ModelsService::class);

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $key = str_random(16);
        $requestHeader = $request->shouldReceive('header')
        ->once()
        ->with('php-auth-user')
        ->andReturn($key);

        Validator::shouldReceive('make')
        ->once()
        ->andReturn(Mockery::mock(['fails' => false]));

        $missionRepository->shouldReceive('find')
        ->once()
        ->with($missionId)
        ->andReturn();

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
        ->once()
        ->with($requestData)
        ->andReturn($defaultLanguage);

        $missionModel = new Mission();
        $missionModel->publication_status = "DRAFT";
        $missionRepository->shouldReceive('getMissionDetailsFromId')
        ->once()
        ->with($missionId, $defaultLanguage->language_id)
        ->andReturn($missionModel);

        $missionRepository->shouldReceive('isMissionDonationImpactLinkedToMission')
        ->once()
        ->with($missionId, $data['impact_donation'][0]['impact_donation_id'])
        ->andReturn();

        $missionRepository->shouldReceive('update')
        ->once()
        ->andReturn();

        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_MISSION_UPDATED');

        $methodResponse = [
            'status' => $apiStatus,
            'message' => $apiMessage
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper->shouldReceive('success')
        ->once()
        ->with($apiStatus, $apiMessage)
        ->andReturn($jsonResponse);

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->update($requestData, $missionId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test remove mission tab by mission_tab_id successfully
    *
    * @return void
    */
    public function testRemoveMissionTabByMissionTabIdSuccess()
    {
        $missionTabId = Uuid::uuid4()->toString();

        $methodResponse = [
            'status'=> Response::HTTP_NO_CONTENT,
            'message'=> trans('messages.success.MESSAGE_MISSION_TAB_DELETED')
        ];

        $JsonResponse = new JsonResponse(
            $methodResponse
        );

        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = new Request();
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
        $modelService = $this->mock(ModelsService::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);

        $this->expectsEvents(UserActivityLogEvent::class);

        $missionRepository->shouldReceive('deleteMissionTabByMissionTabId')
        ->once()
        ->andReturn(true);

        $responseHelper->shouldReceive('success')
        ->once()
        ->with(
            Response::HTTP_NO_CONTENT,
            trans('messages.success.MESSAGE_MISSION_TAB_DELETED')
        )
       ->andReturn($JsonResponse);

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
     * @testdox Test not found mission with impact donation attribute with error status
     */
    public function testImpactDonationMissionNotLinkWithMissionError()
    {
        \DB::setDefaultConnection('tenant');

        $data = [
            'impact_donation' => [
                [
                    'impact_donation_id' => str_random(36),
                    'amount' => rand(100000, 200000),
                    'translations' => [
                        [
                            'language_code' => 'es',
                            'content' => str_random(160)
                        ]
                    ]
                ]
            ],
            'publication_status' => 'DRAFT'
        ];

        $requestData = new Request($data);
        $missionId = rand(50000, 70000);

        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = $this->mock(Request::class);
        $mission = $this->mock(Mission::class);
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);
        $modelsService = $this->mock(ModelsService::class);

        $defaultLanguage = (object)[
            'language_id' => 1,
            'code' => 'en',
            'name' => 'English',
            'default' => '1'
        ];

        $key = str_random(16);
        $requestHeader = $request->shouldReceive('header')
        ->once()
        ->with('php-auth-user')
        ->andReturn($key);

        Validator::shouldReceive('make')
        ->once()
        ->andReturn(Mockery::mock(['fails' => false]));

        $missionRepository->shouldReceive('find')
        ->once()
        ->with($missionId)
        ->andReturn();

        $languageHelper->shouldReceive('getDefaultTenantLanguage')
        ->once()
        ->with($requestData)
        ->andReturn($defaultLanguage);

        $missionModel = new Mission();
        $missionModel->publication_status = 'DRAFT';
        $missionRepository->shouldReceive('getMissionDetailsFromId')
        ->once()
        ->with($missionId, $defaultLanguage->language_id)
        ->andReturn($missionModel);

        $missionRepository->shouldReceive('isMissionDonationImpactLinkedToMission')
        ->once()
        ->with($missionId, $data['impact_donation'][0]['impact_donation_id'])
        ->andThrow($modelNotFoundException);

        $methodResponse = [
            'errors' => [
                [
                    'status' => Response::HTTP_NOT_FOUND,
                    'type' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    'code' =>  config('constants.error_codes.IMPACT_DONATION_MISSION_NOT_FOUND'),
                    'message' => trans('messages.custom_error_message.ERROR_IMPACT_DONATION_MISSION_NOT_FOUND')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.IMPACT_DONATION_MISSION_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_IMPACT_DONATION_MISSION_NOT_FOUND')
        )
        ->andReturn($jsonResponse);

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelsService
        );

        $response = $callController->update($requestData, $missionId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test remove mission tab by mission_tab_id error for mission_tab_id does not found
    *
    * @return void
    */
    public function testRemoveMissionTabByMissionTabIdError()
    {
        $missionTabId = Uuid::uuid4()->toString();

        $methodResponse = [
            'errors'=> [
                [
                    'status'=> Response::HTTP_NOT_FOUND,
                    'type'=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    'code'=> config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
                    'message'=> trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
                ]
            ]
        ];

        $JsonResponse = new JsonResponse(
            $methodResponse
        );

        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = new Request();
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $modelService = $this->mock(ModelsService::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);

        $missionRepository->shouldReceive('deleteMissionTabByMissionTabId')
        ->once()
        ->with($missionTabId)
        ->andThrow($modelNotFoundException);

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
            trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
        )
       ->andReturn($JsonResponse);

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test sort key of mission_tab update is already exist errro
    *
    * @return void
    */
    public function testMissionTabSortKeyExistError()
    {
        $missionTabId = Uuid::uuid4()->toString();
        $data = [
            'mission_tabs' => [
                [
                    'mission_tab_id' => $missionTabId,
                    'sort_key' => rand(100, 200),
                    'translations' => [
                        [
                            'lang' => 'es',
                            'name' => str_random(160),
                            'sections' => [
                                [
                                    'title'=> str_random(20),
                                    'content' => str_random(200)
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $requestData = new Request($data);
        $missionId = rand(50000, 70000);

        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = $this->mock(Request::class);
        $mission = $this->mock(Mission::class);
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);
        $modelService = $this->mock(ModelsService::class);

        $key = str_random(16);
        $requestHeader = $request->shouldReceive('header')
            ->once()
            ->with('php-auth-user')
            ->andReturn($key);

        $missionRepository->shouldReceive('find')
            ->once()
            ->with($missionId)
            ->andReturn();

        Validator::shouldReceive('make')
            ->once()
            ->andReturn(Mockery::mock(['fails' => false]));

        $missionRepository->shouldReceive('checkExistSortKey')
            ->once()
            ->with($missionId, $requestData->mission_tabs)
            ->andReturn(false);

        $methodResponse = [
            'errors'=> [
                [
                    'status'=> Response::HTTP_UNPROCESSABLE_ENTITY,
                    'type'=> Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    'code'=>  config('constants.error_codes.ERROR_SORT_KEY_ALREADY_EXIST'),
                    'message'=> trans('messages.custom_error_message.ERROR_SORT_KEY_ALREADY_EXIST')
                ]
            ]
        ];

        $jsonResponse = $this->getJson($methodResponse);

        $responseHelper->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_SORT_KEY_ALREADY_EXIST'),
                trans('messages.custom_error_message.ERROR_SORT_KEY_ALREADY_EXIST')
            )
            ->andReturn($jsonResponse);

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->update($requestData, $missionId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
     * Create a new service instance.
     *
     * @param  App\Repositories\Mission\MissionRepository $missionRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  Illuminate\Http\Request $request
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Repositories\MissionMedia\MissionMediaRepository $missionMediaRepository
     * @param  App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository $tenantActivatedSettingRepository
     * @param  App\Repositories\Notification\NotificationRepository $notificationRepository
     * @param App\Repositories\Organization\OrganizationRepository $organizationRepository
     * @param  App\Services\Mission\ModelsService $modelService
     * @return void
     */
    private function getController(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        Request $request,
        LanguageHelper $languageHelper,
        MissionMediaRepository $missionMediaRepository,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        NotificationRepository $notificationRepository,
        OrganizationRepository $organizationRepository,
        ModelsService $modelService
    ) {
        return new MissionController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository,
            $organizationRepository,
            $modelService
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

    /**
    * get json reponse
    *
    * @param class name
    *
    * @return JsonResponse
    */
    private function getJson($class)
    {
        return new JsonResponse($class);
    }
    
    /** 
     * Create a new service instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\TimeMission $timeMission
     * @param  App\Models\MissionLanguage $missionLanguage
     * @param  App\Models\MissionDocument $missionDocument
     * @param  App\Models\FavouriteMission $favouriteMission
     * @param  App\Models\MissionSkill $missionSkill
     * @param  App\Models\MissionRating $missionRating
     * @param  App\Models\MissionApplication $missionApplication
     * @param  App\Models\City $city
     * @param  App\Models\MissionTab $missionTab
     * @param  App\Models\MissionTabLanguage $missionTabLanguage
     * @return void
     */
    private function getServices(
        Mission $mission,
        TimeMission $timeMission,
        MissionLanguage $missionLanguage,
        MissionDocument $missionDocument,
        FavouriteMission $favouriteMission,
        MissionSkill $missionSkill,
        MissionRating $missionRating,
        MissionApplication $missionApplication,
        City $city,
        MissionTab $missionTab,
        MissionTabLanguage $missionTabLanguage
    ) {
        return new ModelsService(
            $mission,
            $timeMission,
            $missionLanguage,
            $missionDocument,
            $favouriteMission,
            $missionSkill,
            $missionRating,
            $missionApplication,
            $city,
            $missionTab,
            $missionTabLanguage
        );
    }

    /**
    * get json reponse
    *
    * @param array $data
    * @return JsonResponse
    */
    private function getJson($data)
    {
        return new JsonResponse($data);
    }
}
