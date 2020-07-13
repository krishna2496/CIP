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

class MissionControllerTest extends TestCase
{
    /**
     * A donation store method
     *
     * @return void
     */
    public function testStoreDonationAttribute()
    {
        \DB::setDefaultConnection('tenant');

        $this->assertTrue(true);
        $mission = factory(\App\Models\Mission::class)->make();
      
        $mission->mission_type = config('constants.mission_type.DONATION');
        $missionParam = $mission->toArray();
        $missionParam['goal_amount_currency'] = 'USD';
        $missionParam['goal_amount'] = 233;
        $missionParam['show_goal_amount'] = 0;
        $missionParam['show_donation_percentage'] = 0 ;
        $missionParam['show_donation_meter'] = 0;
        $missionParam['show_donation_count'] = 0;
        $missionParam['show_donors_count'] = 0;
        $missionParam['disable_when_funded'] = 0 ;
        $missionParam['is_disabled'] = 0;
        
        $missionRepositoryMockResponse = new Mission();
        $methodResponse = [
            "status"=> Response::HTTP_CREATED,
            "data"=> [
                "mission_id" => $missionRepositoryMockResponse->mission_id
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
        $missionResult = $missionRepository
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
                ['mission_id' => $missionRepositoryMockResponse->mission_id]
            )
            ->andReturn($JsonResponse); 

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
     * A donation update method
     *
     * @return void
     */
    public function testUpdateDonationAttribute()
    {
        \DB::setDefaultConnection('tenant');
        $missionId = 13;
        $this->assertTrue(true);
        $mission = factory(\App\Models\Mission::class)->make();
      
        $mission->mission_type = config('constants.mission_type.DONATION');
        $missionParam = $mission->toArray();
        $missionParam['goal_amount_currency'] = 'CAD';
        $missionParam['goal_amount'] = 233;
        $missionParam['show_goal_amount'] = 0;
        $missionParam['show_donation_percentage'] = 0 ;
        $missionParam['show_donation_meter'] = 0;
        $missionParam['show_donation_count'] = 0;
        $missionParam['show_donors_count'] = 0;
        $missionParam['disable_when_funded'] = 0 ;
        $missionParam['is_disabled'] = 0;
        
        $missionRepositoryMockResponse = new Mission();
        $methodResponse = [
            "status"=> Response::HTTP_OK,
            "data"=> [
                "mission_id" => $missionRepositoryMockResponse->mission_id
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
        $missionResult = $missionRepository
            ->shouldReceive('update')
            ->once()
            ->with($request)
            ->andReturn($missionRepositoryMockResponse);
           
        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper
            ->shouldReceive('success')
            ->once()
            ->with(
                Response::HTTP_OK,
                trans('messages.success.MESSAGE_MISSION_ADDED'),
                ['mission_id' => $missionRepositoryMockResponse->mission_id]
            )
            ->andReturn($JsonResponse); 

        $callController = $this->getController(
            $missionRepository,
            $responseHelper,
            $request,
            $languageHelper,
            $missionMediaRepository,
            $tenantActivatedSettingRepository,
            $notificationRepository
        );
        
        $response = $callController->update($request,$missionId);
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
