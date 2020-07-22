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

class MissionControllerTest extends TestCase
{
    /**
     * @testdox Test udpate mission with impact donation attribute with success status
     */
    public function testUpdateImpactDonationAttributeSuccess()
    {
        \DB::setDefaultConnection('tenant');

        $data = [
            "impact_donation" => [
                [
                    "impact_donation_id" => str_random(36),
                    "amount" => rand(100000, 200000),
                    "translations" => [
                        [
                            "language_code" => "es",
                            "content" => str_random(160)
                        ]
                    ]
                ],
                [
                    "amount" => rand(100000, 200000),
                    "translations" => [
                        [
                            "language_code" => "ab",
                            "content" => str_random(160)
                        ]
                    ]
                ]
            ],
            "publication_status" => "DRAFT"
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

        $defaultLanguage = (object)[
            "language_id" => 1,
            "code" => "en",
            "name" => "English",
            "default" => "1"
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
            "status" => $apiStatus,
            "message" => $apiMessage
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
            $notificationRepository
        );

        $response = $callController->update($requestData, $missionId);
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
            "impact_donation" => [
                [
                    "impact_donation_id" => str_random(36),
                    "amount" => rand(100000, 200000),
                    "translations" => [
                        [
                            "language_code" => "es",
                            "content" => str_random(160)
                        ]
                    ]
                ]
            ],
            "publication_status" => "DRAFT"
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

        $defaultLanguage = (object)[
            "language_id" => 1,
            "code" => "en",
            "name" => "English",
            "default" => "1"
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
        ->andThrow($modelNotFoundException);

        $methodResponse = [
            "errors"=> [
                [
                    "status"=> Response::HTTP_NOT_FOUND,
                    "type"=> Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    "code"=>  config('constants.error_codes.IMPACT_DONATION_MISSION_NOT_FOUND'),
                    "message"=> trans('messages.custom_error_message.ERROR_IMPACT_DONATION_MISSION_NOT_FOUND')
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
            $notificationRepository
        );

        $response = $callController->update($requestData, $missionId);
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
}
