<?php

namespace Tests\Unit\Services\Admin;

use App\Services\Mission\AdminMissionTransformService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use TestCase;
use App\Helpers\LanguageHelper;
use App\Models\Mission;

class AdminMissionTransformServiceTest extends TestCase
{
    /**
    * @testdox Test mission transform
    *
    * @return void
    */
    public function testMissionTransformSuccess()
    {
        $languageHelper = $this->mock(LanguageHelper::class);

        $service = $this->getService(
            $languageHelper
        );

        $response = $service->transfromAdminMission($mission);
    }

    /**
     * Create a new service instance.
     *
     * @param  App\Helpers\LanguageHelper $languageHelper
     *
     * @return void
     */
    private function getService(
        LanguageHelper $languageHelper
    ) {
        return new AdminMissionTransformService(
            $languageHelper
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
