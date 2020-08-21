<?php

namespace Tests\Unit\Http\Controllers\App\Auth;

use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\TenantHasSettings;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use Mockery;
use TestCase;
use Closure;

class TenantHasSettingsTest extends TestCase
{
    public function test_handle_unauthorize_request()
    {
        $request = new Request;
        $settings = 'volunteering';
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);

        $callbackMock = $this->getMockBuilder(\stdClass::class)
        ->setMethods(['__invoke'])
        ->getMock();

        $closure = function (...$args) use ($callbackMock) {
            return $callbackMock(...$args);
        };

        $tenantActivatedSettingRepository->shouldReceive('checkTenantSettingStatus')
        ->once()
        ->andReturn(false);

        $responseHelper->shouldReceive('error')
            ->once()
            ->with(
                Response::HTTP_FORBIDDEN,
                Response::$statusTexts[Response::HTTP_FORBIDDEN],
                '',
                trans('messages.custom_error_message.ERROR_UNAUTHORIZED')
            );

        $middleware = new TenantHasSettings(
            $tenantActivatedSettingRepository,
            $responseHelper
        );

        $response = $middleware->handle($request, $closure, $settings);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_handle_authorize_request()
    {
        $request = new Request;
        $settings = 'volunteering';
        $tenantActivatedSettingRepository = $this->mock(TenantActivatedSettingRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);

        $callbackMock = $this->getMockBuilder(\stdClass::class)
        ->setMethods(['__invoke'])
        ->getMock();

        $closure = function (...$args) use ($callbackMock) {
            return $callbackMock(...$args);
        };

        $tenantActivatedSettingRepository->shouldReceive('checkTenantSettingStatus')
        ->once()
        ->andReturn(true);

        $middleware = new TenantHasSettings(
            $tenantActivatedSettingRepository,
            $responseHelper
        );

        $response = $middleware->handle($request, $closure, $settings);
        $this->assertNull($response);
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
