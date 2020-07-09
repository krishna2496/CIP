<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Mockery;
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
    public function testExample()
    {
        $this->assertTrue(true);
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
