<?php
	
namespace Tests\Unit\Http\Controllers\Admin\Tenant;

use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin\Tenant\TenantActivatedSettingController;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use TestCase;

class TenantActivatedSettingControllerTest extends TestCase
{

    /**
    * @testdox Test index with success status
    *
    * @return void
    */
    public function testIndexSuccess()
    {
        $request = new Request();
        $mockResponse = $this->mockGetAllTenantSettingResponse();

        $helper = $this->mock(Helpers::class);
        $helper->shouldReceive('getAllTenantSetting')
            ->once()
            ->with($request)
            ->andReturn($mockResponse);

        $repository = $this->mock(TenantActivatedSettingRepository::class);
        $repository->shouldReceive('fetchAllTenantSettings')
            ->once()
            ->andReturn(new Collection([
                (Object) [
                    'tenant_setting_id' => 2,
                    'settings' => (Object) [
                        'setting_id' => 1
                    ]
                ]
            ]));

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success')
            ->once()
            ->with(Response::HTTP_OK, 'Settings listed successfully', [
                $mockResponse->first()
            ]);

        $controller = $this->getController(
            $repository,
            $responseHelper,
            $helper
        );

        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
    * @testdox Test index with not found status
    *
    * @return void
    */
    public function testIndexNotFound()
    {
        $request = new Request();
        $mockResponse = new Collection([]);

        $helper = $this->mock(Helpers::class);
        $helper->shouldReceive('getAllTenantSetting')
            ->once()
            ->with($request)
            ->andReturn($mockResponse);

        $repository = $this->mock(TenantActivatedSettingRepository::class);
        $repository->shouldReceive('fetchAllTenantSettings')
            ->once()
            ->andReturn(new Collection([]));

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success')
            ->once()
            ->with(Response::HTTP_OK, 'No records found', []);

        $controller = $this->getController(
            $repository,
            $responseHelper,
            $helper
        );

        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    private function mockGetAllTenantSettingResponse()
    {
        return new Collection([
            (Object) [
                'tenant_setting_id' => 1,
                'key' => 'total_votes',
                'description' => 'setting description',
                'title' => 'Total Votes In The Platform'
            ],
            (Object) [
                'tenant_setting_id' => 2,
                'key' => 'skills_enabled',
                'description' => 'User profile edit page - Add new skills (Allow the user to add or manage his skills. If enabled open modal)',
                'title' => 'skills enabled'
            ]
        ]);  
    }

    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository $tenantActivatedSettingRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\Helpers $helpers
     * 
     * @return void
     */
    private function getController(
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers
    ) {
        return new TenantActivatedSettingController(
            $tenantActivatedSettingRepository,
            $responseHelper,
            $helpers
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