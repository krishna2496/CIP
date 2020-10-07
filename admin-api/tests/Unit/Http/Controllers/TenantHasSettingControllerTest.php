<?php

namespace Tests\Unit\Http\Controllers;

use TestCase;
use Mockery;
use Validator;
use App\Http\Controllers\TenantHasSettingController;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Repositories\Tenant\TenantRepository;
use App\Repositories\TenantHasSetting\TenantHasSettingRepository;
use App\Helpers\DatabaseHelper;
use App\Models\Tenant;

class TenantHasSettingControllerTest extends TestCase
{
    /**
     * @testdox Test store check volunteer time or goal should be enabled at time
     *
     * @return void
     */
    public function testStoreCheckVolunteeringTimeGoalCondition()
    {
        $tenantHasSettingRepository = $this->mock(TenantHasSettingRepository::class);
        $tenantRepository = $this->mock(TenantRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $databaseHelper = $this->mock(DatabaseHelper::class);

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->shouldReceive('fails')
            ->andReturn(false);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $tenantId = rand();
        $requestData = [];
        $request = new Request();

        $tenantRepository->shouldReceive('find')
            ->once()
            ->andReturn(new Tenant());

        $tenantHasSettingRepository->shouldReceive('checkVolunteeringTimeAndGoalSetting')
            ->once()
            ->andReturn(trans('messages.custom_error_message.ERROR_VOLUNTEERING_TIME_OR_GOAL_SHOULD_BE_ACTIVE'));
        
        $responseHelper->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_VOLUNTEERING_TIME_OR_GOAL_SHOULD_BE_ACTIVE'),
                trans('messages.custom_error_message.ERROR_VOLUNTEERING_TIME_OR_GOAL_SHOULD_BE_ACTIVE'),
            )
            ->andReturn(new JsonResponse());

        $controller = $this->getController(
            $tenantHasSettingRepository,
            $tenantRepository,
            $responseHelper,
            $databaseHelper
        );

        $response = $controller->store($request, $tenantId);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\TenantHasSetting\TenantHasSettingRepository $tenantHasSettingRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\DatabaseHelper $databaseHelper
     * @return void
     */
    private function getController(
        TenantHasSettingRepository $tenantHasSettingRepository,
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper,
        DatabaseHelper $databaseHelper
    ) {
        return new TenantHasSettingController(
            $tenantHasSettingRepository,
            $tenantRepository,
            $responseHelper,
            $databaseHelper
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
