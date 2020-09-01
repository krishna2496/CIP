<?php
<<<<<<< HEAD

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
=======
namespace Tests\Unit\Http\Controllers\App\Mission;

use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Http\Controllers\Admin\Mission\MissionController;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Mission\MissionRepository;
use App\Events\User\UserActivityLogEvent;
use Illuminate\Http\JsonResponse;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use TestCase;
use Mockery;
use App\Services\Mission\ModelsService;
use App\Models\Mission;
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
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8

class MissionControllerTest extends TestCase
{
    /**
<<<<<<< HEAD
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

=======
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
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
<<<<<<< HEAD
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = $this->mock(Request::class);
        $mission = $this->mock(Mission::class);

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
=======
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
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
<<<<<<< HEAD
            $notificationRepository
        );

        $response = $callController->update($requestData, $missionId);
=======
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
<<<<<<< HEAD
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

=======
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
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $languageHelper = $this->mock(LanguageHelper::class);
        $missionMediaRepository = $this->mock(MissionMediaRepository::class);
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $notificationRepository = $this->mock(NotificationRepository::class);
<<<<<<< HEAD
        $missionRepository = $this->mock(MissionRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $request = $this->mock(Request::class);
        $mission = $this->mock(Mission::class);
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);

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

=======
        $modelNotFoundException = $this->mock(ModelNotFoundException::class);
        $modelService = $this->mock(ModelsService::class);
        $organizationRepository = $this->mock(OrganizationRepository::class);

        $missionRepository->shouldReceive('deleteMissionTabByMissionTabId')
        ->once()
        ->with($missionTabId)
        ->andThrow($modelNotFoundException);

>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
<<<<<<< HEAD
            config('constants.error_codes.IMPACT_DONATION_MISSION_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_IMPACT_DONATION_MISSION_NOT_FOUND')
        )
        ->andReturn($jsonResponse);
=======
            config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
            trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
        )
       ->andReturn($JsonResponse);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
<<<<<<< HEAD
            $notificationRepository
        );

        $response = $callController->update($requestData, $missionId);
=======
            $notificationRepository,
            $organizationRepository,
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
<<<<<<< HEAD
    * Create a new service instance.
    *
    * @param  App\Services\UserService $userService
    *
    * @return void
    */
=======
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
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
    private function getController(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        Request $request,
        LanguageHelper $languageHelper,
        MissionMediaRepository $missionMediaRepository,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
<<<<<<< HEAD
        NotificationRepository $notificationRepository
=======
        NotificationRepository $notificationRepository,
        OrganizationRepository $organizationRepository,
        ModelsService $modelService
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
    ) {
        return new MissionController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
<<<<<<< HEAD
            $notificationRepository
=======
            $notificationRepository,
            $organizationRepository,
            $modelService
>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
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
<<<<<<< HEAD
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
=======
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

>>>>>>> e68ac8b317ac6c6eb3cdbf32c8861c47af0f2be8
}
