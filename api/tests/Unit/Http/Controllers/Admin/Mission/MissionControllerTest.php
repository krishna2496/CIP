<?php
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

class MissionControllerTest extends TestCase
{
    /**
    * @testdox Test remove mission tab by mission_tab_id successfully
    *
    * @return void
    */
    public function testRemoveMissionTabByMissionTabIdSuccess()
    {
        $missionTabId = Uuid::uuid4()->toString();

        $methodResponse = [
            "status"=> Response::HTTP_NO_CONTENT,
            "message"=> trans('messages.success.MESSAGE_MISSION_TAB_DELETED')
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
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
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
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
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
            $modelService
        );

        $response = $callController->removeMissionTab($missionTabId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test remove mission tab by mission_id successfully
    *
    * @return void
    */
    public function testRemoveMissionTabByMissionIdSuccess()
    {
        $missionId = rand(50000, 70000);
        $methodResponse = [
            "status"=> Response::HTTP_NO_CONTENT,
            "message"=> trans('messages.success.MESSAGE_MISSION_TAB_DELETED')
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
        
        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);

        $this->expectsEvents(UserActivityLogEvent::class);

        $modelService = $this->getServices(
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

        $modelService->mission->shouldReceive('findOrFail')
        ->once()
        ->with($missionId)
        ->andReturn();

        $missionRepository->shouldReceive('deleteMissionTabBymissionId')
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
            $modelService
        );

        $response = $callController->removeMissionTab($missionId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test remove mission tab by 0mission_id error for mission_id does not found
    *
    * @return void
    */
    public function testRemoveMissionTabByMissionIdError()
    {
        $missionId = rand(50000, 70000);
        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
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

        $mission = $this->mock(Mission::class);
        $timeMission = $this->mock(TimeMission::class);
        $missionLanguage = $this->mock(MissionLanguage::class);
        $missionDocument = $this->mock(MissionDocument::class);
        $favouriteMission = $this->mock(FavouriteMission::class);
        $missionSkill = $this->mock(MissionSkill::class);
        $missionRating = $this->mock(MissionRating::class);
        $missionApplication = $this->mock(MissionApplication::class);
        $city = $this->mock(City::class);
        $missionTab = $this->mock(MissionTab::class);
        $missionTabLanguage = $this->mock(MissionTabLanguage::class);

        $modelService = $this->getServices(
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

        $modelService->mission->shouldReceive('findOrFail')
        ->once()
        ->with($missionId)
        ->andThrow($modelNotFoundException);

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
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
            $modelService
        );

        $response = $callController->removeMissionTab($missionId);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));
    }

    /**
    * @testdox Test remove mission tab by mission_id error for non numeric mission id does not found
    *
    * @return void
    */
    public function testRemoveMissionTabByNonNumericMissionIdError()
    {
        $missionId = rand(50000, 70000).str_random(5);
        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=> config('constants.error_codes.MISSION_TAB_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.MISSION_TAB_NOT_FOUND')
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

        $responseHelper->shouldReceive('error')
        ->once()
        ->with(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
            trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
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
            $modelService
        );

        $response = $callController->removeMissionTab($missionId);
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

}
